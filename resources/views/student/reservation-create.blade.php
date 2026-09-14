@extends('layouts.app')

@section('content')
<section class="form-card">
    <h1>Nueva reserva</h1>
    <form method="POST" action="{{ route('student.reservations.store') }}">
        @csrf
        <label for="machine_id">Máquina</label>
        <select id="machine_id" name="machine_id" required>
            <option value="">Selecciona una máquina</option>
            @foreach($machines as $machine)<option value="{{ $machine->id }}" @selected(old('machine_id') == $machine->id)>{{ $machine->name }}</option>@endforeach
        </select>

        <label for="reservation_date">Fecha</label>
        <input id="reservation_date" name="reservation_date" type="date" value="{{ old('reservation_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required>

        <label for="schedule">Horario</label>
        <select id="schedule" name="schedule" required>
            <option value="">Selecciona un horario</option>
            @foreach($schedules as $slot)<option value="{{ $slot }}" @selected(old('schedule') === $slot)>{{ $slot }}</option>@endforeach
        </select>

        <label for="garment_count">Cantidad de prendas</label>
        <input id="garment_count" name="garment_count" type="number" min="1" value="{{ old('garment_count', 1) }}" required>

        <button type="submit">Confirmar reserva</button>
    </form>
</section>
@endsection
