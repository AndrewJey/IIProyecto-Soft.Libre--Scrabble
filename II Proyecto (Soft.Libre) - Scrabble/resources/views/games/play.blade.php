@extends('app')

@section('content')

@if ( $status )
	<div class="start-game">
		<a href="/gamestart/{{{$game_channel}}}" class="btn btn-primary btn-raised">Start Game</a>
	</div>
@else
	<div class="start-game col-md-12 row">
		<a href="#" class="btn btn-primary btn-raised play-game">Play Game</a>
		<a href="#" class="btn btn-primary btn-raised reset-letters">Reset 3 Letters</a>
		<a href="#" class="btn btn-primary btn-raised give-up">Give Up</a>
		<a href="#" class="btn btn-primary btn-raised turn-pass">Turn Pass</a>
	</div>
@endif


<div class="col-md-3">
	<table id="table-players">

	</table>
</div>
<div class="col-md-9 text-center">
	<h1>WORDS</h1>
	<table class="table-letters">
		<tr>
			<td class="empty-cell"></td>
			<td class="empty-cell"></td>
			<td class="empty-cell"></td>
			<td class="empty-cell"></td>
			<td class="empty-cell"></td>
			<td class="empty-cell"></td>
			<td class="empty-cell"></td>
		</tr>
	</table>
</div>
<div class="col-md-9">
	<h1 class="text-center">BOARD</h1>
	<table id="table-game">

	</table>
</div>
@endsection

@section('scripts')

	<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
	<script src="/js/jquery-custom.js"></script>
	<script src="/js/jquery.sortable.min.js"></script>
	<script src="/js/gamejoin.js"></script>
	<script>
		var socket = io('http://10.60.29.225:3000');

		socket.on('{{{$game_channel}}}:App\\Events\\GameJoin', function(message){
			var board = getBoardDatas();
			board.done(function(data){
				chargeBoard(data);
			});
			var player = getPlayerList();
			player.done(function(data){
				chargePlayersList(data);
			});
		});

		socket.on('{{{$game_channel}}}:App\\Events\\GameStart', function(message){
			removeStartButton();
			chargeOnClick();
			var board = getBoardDatas();
			board.done(function(data){
				chargeBoard(data);
			});
			var player = getPlayerList();
			player.done(function(data){
				chargePlayersList(data);
			});
			var letter = getLetterDatas();
			letter.done(function(data){
				chargeLettersList(data);
			});
			chargeDragDrop();
		});

		socket.on('{{{$game_channel}}}:App\\Events\\GiveUp', function(message){
			custom_success(message.data.msg);
		});

		socket.on('{{{$game_channel}}}:App\\Events\\TurnPass', function(message){
			var board = getBoardDatas();
			board.done(function(data){
				chargeBoard(data);
			});
			var player = getPlayerList();
			player.done(function(data){
				chargePlayersList(data);
			});
			var letter = getLetterDatas();
			letter.done(function(data){
				chargeLettersList(data);
			});
			custom_success(message.data.msg);
		});

	</script>
@endsection
