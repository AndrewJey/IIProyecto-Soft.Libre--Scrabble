@extends('app')

@section('content')
<div class="jumbotron">
<div class="container">
	<div class="row">
		<form class="form-horizontal" action="/games" method="POST">
            <fieldset>
                <legend>New Game</legend>
                <div class="form-group">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <label class="col-lg-2 control-label">Name</label>
                    <div class="col-lg-9">
                        <input class="form-control" type="text" name="name" required>
                    </div>
                    <label class="col-lg-2 control-label">Minimum number of players</label>
                    <div class="col-lg-9">
                        <select name="players" class="form-control" id="inputPlayers" required>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-8 col-lg-offset-2">
                        <a href="/games" class="btn btn-default btn-raised">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </fieldset>
        </form>
	</div>
</div>
</div>
<script>

    </script>
@endsection