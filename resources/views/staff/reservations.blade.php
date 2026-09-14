@extends('layouts.app')

@section('content')
<h1>Reservas</h1>
<p class="muted">Visualiza las reservas y actualiza su estado.</p>
@if($reservations->isEmpty())
    <div class="card"><p>No hay reservas registradas.</p></div>
@else
<div class="table-wrap"><table>
    <thead><tr><th>Estudiante</th><th>Máquina</th><th>Fecha</th><th>Horario</th><th>Prendas</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    @foreach($reservations as $reservation)
        <tr>
            <td>{{ $reservation->student->name }}<br><small>{{ $reservation->student->email }}</small></td>
            <td>{{ $reservation->machine->name }}</td>
            <td>{{ $reservation->reservation_date->format('d/m/Y') }}</td>
            <td>{{ $reservation->schedule }}</td>
            <td>{{ $reservation->garment_count }}</td>
            <td><span class="status">{{ ucfirst($reservation->status) }}</span></td>
            <td><form method="POST" action="{{ route('staff.reservations.status', $reservation) }}" class="status-form">
                @csrf @method('PATCH')
                <select name="status"><option value="pendiente" @selected($reservation->status === 'pendiente')>Pendiente</option><option value="en proceso" @selected($reservation->status === 'en proceso')>En proceso</option><option value="finalizada" @selected($reservation->status === 'finalizada')>Finalizada</option><option value="cancelada" @selected($reservation->status === 'cancelada')>Cancelada</option></select>
                <button type="submit">Guardar</button>
            </form></td>
        </tr>
    @endforeach
    </tbody>
</table></div>
@endif
@endsection
