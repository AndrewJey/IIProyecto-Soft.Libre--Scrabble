$( document ).ready(function() {
	var datas = getBoardDatas();
	datas.done(function(data){
		chargeBoard(data);
	});
	datas = getPlayerList();
	datas.done(function(data){
		chargePlayersList(data);
	});
	datas = getLetterDatas();
	datas.done(function(data){
		chargeLettersList(data);
		chargeDragDrop();
	});
  chargeOnClick();
});

function chargeOnClick(){
  $(".start-game .play-game").on( "click", function() {
    updateValues();
  });
  $(".start-game .give-up").on( "click", function() {
    give_up();
  });
  $(".start-game .turn-pass").on( "click", function() {
    turn_pass();
  });
  $(".start-game .reset-letters").on( "click", function() {
    reset_letters();
  });
}

function getBoardDatas(){
	var url = "/gameBoard/";
	index = (window.location.href).lastIndexOf("/");
	id = (window.location.href).substring(index+1);
	url += id;
	return $.get(url);
}

function getPlayerList(){
	var url = "/playersList/";
	index = (window.location.href).lastIndexOf("/");
	id = (window.location.href).substring(index+1);
	url += id;
	return $.get(url);
}

function getLetterDatas(){
	var url = "/letterList/";
	index = (window.location.href).lastIndexOf("/");
	id = (window.location.href).substring(index+1);
	url += id;
	return $.get(url);
}

function chargeBoard(data){
	$('#table-game').empty();
	var table = '';
	$.each( data, function( key1, value1 ) {
		table += '<tr>';
		$.each( value1, function( key2, value2 ) {
			var types = ['<div<span>X2 L</span>', '<div<span>X3 L</span>', '<div<span>X2 W</span>', '<div<span>X3 W</span>'];
			var letter = '';
			if (value2.letter) {
				letter = '<div class="Tile Temp"><span class="Letter" data-letter="'+value2.letter.letter+'">'+value2.letter.letter+'</span><span class="Score">'+value2.letter.points+'</span></div>';
				types = ['', '', '', ''];
			}
			if (value2.type == 0) {
				table += '<td class="ui-droppable target connected normal-cell"><div<span>&nbsp;</span>';
			}
			else if(value2.type == 1){
				if (value2.multiplier == 2) {
					table += '<td class="ui-droppable target connected double-letter-score">'+types[0];
				}
				else if(value2.multiplier == 3){
					table += '<td class="ui-droppable target connected triple-letter-score">'+types[1];
				}
			}
			else if(value2.type == 2){
				if (value2.multiplier == 2) {
					table += '<td class="ui-droppable target connected double-word-score">'+types[2];
				}
				else if(value2.multiplier == 3){
					table += '<td class="ui-droppable target connected triple-word-score">'+types[3];
				}
			}
			table += letter + '</td>';
		});
		table += '</tr>';
	});
	$('#table-game').append(table);
}

function chargePlayersList(data){
	$('#table-players').empty();
	var table = '<tr><th>Name</th><th>Points<th>Turno</th></tr>';
	$.each( data, function( key, value ) {
		table += '<tr>';
		table += '<td>'+ value['first_name'] +'</td>';
		table += '<td>'+ value['points'] +'</td>';
    table += '<td>'+ value['turno'] +'</td>';
		table += '</tr>';
	});
	$('#table-players').append(table);
}

function removeStartButton(){
	$( ".start-game" ).empty();
  var text = '<div class="start-game col-md-12 row"><a href="#" class="btn btn-primary btn-raised play-game">Play Game</a><a href="#" class="btn btn-primary btn-raised give-up">Give Up</a><a href="#" class="btn btn-primary btn-raised turn-pass">Turn Pass</a><a href="#" class="btn btn-primary btn-raised turn-pass">Turn Pass</a></div>';
  $('.start-game').append(text)
}

function chargeLettersList(data){
	$('.table-letters').empty();
	var table = '<tr>';
	$.each( data, function( key, value ) {
		table += '<td class="source connected normal-cell"><div class="Tile Temp ui-draggable"><a><span class="Letter" data-letter="'+value.letter+'">'+value.letter+'</span></a></div></td>';
	});
	table += '</tr>';
	$('.table-letters').append(table);
	chargeDragDrop();
}

function chargeDragDrop(){
	$(".source, .target").sortable({
      connectWith: ".connected",
      revert: true,
      handle: "a span"
    });
}

function updateValues() {
	var url = '/validateword/';
	var index_l = (window.location.href).lastIndexOf("/");
	var id_l = (window.location.href).substring(index_l+1);
	url += id_l;
	var items = [];
    $("td.target").children().each(function() {

    	var text = $(this).text();
    	text = text.trim();
    	if (text.length == 1) {
    		var x = $(this).parent()[0].cellIndex + 1;
    		var y = $(this).parent().parent()[0].rowIndex + 1;
    		var item = {
    			x: y,
            	y: x,
            	letter: text
           	};
      		items.push(item);
    	};
    });
    if (items.length > 0) {
    $.ajax({
	  type: 'POST',
	  url: url,
	  data: {letters: items}
	})
	.done(function(data){
		if (data.errors) {
			custom_error(data['msg']);
		}
		else{
			custom_success(data['msg']);
		}
	});
	}
}

function give_up(){
	var url = '/giveup/';
	var index_l = (window.location.href).lastIndexOf("/");
	var id_l = (window.location.href).substring(index_l+1);
	url += id_l;
	$.ajax({
	  type: 'POST',
	  url: url
	})
  .done(function(data){
    if (data.errors) {
      custom_error(data.msg);
    }
    else{
      custom_success(data.msg);
    }
  });
}

function turn_pass(){
	var url = '/turnpass/';
	var index_l = (window.location.href).lastIndexOf("/");
	var id_l = (window.location.href).substring(index_l+1);
	url += id_l;
	$.ajax({
	  type: 'POST',
	  url: url
	}).done(function(data){
    if (data.errors) {
      custom_error(data.msg);
    }
    else{
      custom_success(data.msg);
    }
  });
}

function reset_letters(){
	var url = '/random_letter/';
	var index_l = (window.location.href).lastIndexOf("/");
	var id_l = (window.location.href).substring(index_l+1);
	url += id_l;
	$.ajax({
	  type: 'POST',
	  url: url
	}).done(function(data){
    if (data.errors) {
      custom_error(data.msg);
    }
    else{
      custom_success(data.msg);
      var datas = getLetterDatas();
      datas.done(function(inf){
		chargeLettersList(inf);
		chargeDragDrop();
	  });
    }
  });
}

$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
});

function custom_error(msg){
  var selector = $('.msg-error-permissions');
  selector.text(msg);
  selector.removeClass('hidden');
  selector.fadeTo(1000, 250).slideUp(250, function(){
      selector.addClass('hidden');
  })
};

function custom_success(msg){
  var selector =  $('.msg-success');
  selector.text(msg);
  selector.removeClass('hidden');
  selector.fadeTo(1000, 250).slideUp(250, function(){
      selector.addClass('hidden');
  })
};
