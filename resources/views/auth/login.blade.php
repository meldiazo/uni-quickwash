@extends('layouts.app')

@section('content')
<section class="auth-card">
    <h1>Iniciar sesión</h1>
    <p class="muted">Ingresa a Univalle QuickWash.</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Ingresar</button>
    </form>
    <p>¿Eres estudiante y no tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
</section>
@endsection
