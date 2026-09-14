@extends('layouts.app')

@section('content')
<section class="auth-card">
    <h1>Crear cuenta de estudiante</h1>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <label for="name">Nombre</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>

        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" required>

        <label for="password_confirmation">Confirmar contraseña</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button type="submit">Registrarme</button>
    </form>
    <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
</section>
@endsection
