
<h1>Login</h1>

{{ $errors->first('email') }}

{!! Form::open( ['route' => 'login.process'] ) !!}

	{!! Form::label('email', 'Email') !!}
	{!! Form::email('email') !!}

	{!! Form::label('password', 'Password') !!}
	{!! Form::password('password') !!}

	<label>
		<input type="checkbox" name="remember"> Remember Me
	</label>

	{!! Form::submit('Login') !!}

{!! Form::close() !!}
