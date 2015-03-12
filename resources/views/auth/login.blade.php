
<h1>Login</h1>

{{ $errors->first('login') }}

{!! Form::open( ['route' => 'login.process'] ) !!}
	{!! @todo notices !!}
	{{ $errors->first('email') }}
	{!! Form::label('email', 'Email') !!}
	{!! Form::email('email') !!}

	{{ $errors->first('password') }}
	{!! Form::label('password', 'Password') !!}
	{!! Form::password('password') !!}

	<label>
		<input type="checkbox" name="remember"> Remember Me
	</label>

	{!! Form::submit('Login') !!}

{!! Form::close() !!}
