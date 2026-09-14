@extends('layouts.app')

@section('content')
<h1>Máquinas disponibles</h1>
<p class="muted">Consulta la disponibilidad según una fecha y un horario.</p>
<form method="GET" class="filter-form">
    <div><label for="date">Fecha</label><input id="date" name="date" type="date" value="{{ $date }}" min="{{ now()->toDateString() }}"></div>
    <div><label for="schedule">Horario</label><select id="schedule" name="schedule"><option value="">Selecciona</option>@foreach(\App\Http\Controllers\StudentController::SCHEDULES as $slot)<option value="{{ $slot }}" @selected($schedule === $slot)>{{ $slot }}</option>@endforeach</select></div>
    <button type="submit">Consultar</button>
</form>
<div class="card-grid">
    @foreach($machines as $machine)
        <article class="card"><h2>{{ $machine->name }}</h2>
            @if($date && $schedule)
                <p class="machine-status">{{ $machine->isAvailable($date, $schedule) ? 'Disponible' : 'No disponible' }}</p>
                <p class="muted">{{ $date }} · {{ $schedule }}</p>
            @else <p class="machine-status">Selecciona fecha y horario</p> @endif
        </article>
    @endforeach
</div>
@endsection
