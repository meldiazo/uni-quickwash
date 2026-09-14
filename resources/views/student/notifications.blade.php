@extends('layouts.app')

@section('content')
<section class="form-card">
    <h1>Notificaciones</h1>
    <p class="muted">Recibe avisos dentro del sistema sobre reservas próximas y recogida de ropa.</p>
    <form method="POST" action="{{ route('student.notifications.update') }}">
        @csrf @method('PATCH')
        <label class="checkbox"><input type="checkbox" name="notifications_enabled" value="1" @checked($enabled)> Habilitar notificaciones</label>
        <button type="submit">Guardar preferencia</button>
    </form>

    @if($enabled)
        <h2>Avisos actuales</h2>
        @forelse($notifications as $notification)<div class="notification">{{ $notification }}</div>@empty
            <p class="muted">No hay avisos por ahora.</p>
        @endforelse
    @else
        <p class="muted">Las notificaciones están deshabilitadas.</p>
    @endif
</section>
@endsection
