@extends('app')

@section('content')
@if ( Session::has( 'warning' ) )
<div class="alert alert-dismissable alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>
	{{{ Session::get( 'warning' ) }}}
</div>
@elseif ( Session::has( 'success' ) )
<div class="alert alert-dismissable alert-success">
	<button type="button" class="close" data-dismiss="alert">×</button>
	{{{ Session::get( 'success' ) }}}
</div>
@endif
<a href="/games/new" class="btn btn-primary btn-raised">New Game</a>
<div class="container row">
	<div class="col-md-6 list">
		<ol id="all_games">
			@if ( count($all_games) > 0 )
			<h2>Available games</h2>
			@endif
			@foreach ($all_games as $game)
			<li><a href="/gamejoin/{{ $game->id }}"><p>{{ $game->name }} </p></a></li>
			@endforeach
		</ol>
	</div>
	<div class="col-md-6 list">
		<ol>
		@if ( count($user_games) > 0 )
		<h2>My Games</h2>
		@endif
			@foreach ($user_games as $game)
			<li><a href="/gamejoin/{{ $game->game_id }}"><p>{{ $game->name }} </p></a></li>
			@endforeach
		</ol>
	</div>
</div>
    <script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
    <script>
        var socket = io('http://10.60.29.225:3000');

        socket.on('all-games:App\\Events\\GamesRefresh', function(message){

        $('#all_games').append('<li><a href="/gamejoin/'+message.data.game.id+'"><p>'+message.data.game.name+'</p></a></li>');
        
        });
    </script>
@endsection