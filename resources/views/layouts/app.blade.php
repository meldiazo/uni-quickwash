<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Univalle QuickWash' }}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<header class="site-header">
    <div class="container header-content">
        <a class="brand" href="{{ url('/') }}">Univalle QuickWash</a>
        @auth
            <nav>
                @if(auth()->user()->isStudent())
                    <a href="{{ route('student.machines') }}">Máquinas</a>
                    <a href="{{ route('student.reservations') }}">Mis reservas</a>
                    <a href="{{ route('student.notifications') }}">Notificaciones</a>
                @else
                    <a href="{{ route('staff.machines') }}">Máquinas</a>
                    <a href="{{ route('staff.reservations') }}">Reservas</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button class="link-button" type="submit">Cerrar sesión</button>
                </form>
            </nav>
        @endauth
    </div>
</header>

<main class="container">
    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert error">
            <ul>
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
