@extends('layouts.app')

@section('content')
<div class="page-heading">
    <div><h1>Mis reservas</h1><p class="muted">Aquí puedes consultar y cancelar reservas pendientes.</p></div>
    <a class="button" href="{{ route('student.reservations.create') }}">Nueva reserva</a>
</div>

@if($notifications)
    <div class="notification-list">
        @foreach($notifications as $notification)<p>{{ $notification }}</p>@endforeach
    </div>
@endif

@if($reservations->isEmpty())
    <div class="card"><p>Aún no tienes reservas.</p></div>
@else
    <div class="table-wrap"><table>
        <thead><tr><th>Máquina</th><th>Fecha</th><th>Horario</th><th>Prendas</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @foreach($reservations as $reservation)
            <tr>
                <td>{{ $reservation->machine->name }}</td>
                <td>{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                <td>{{ $reservation->schedule }}</td>
                <td>{{ $reservation->garment_count }}</td>
                <td><span class="status">{{ ucfirst($reservation->status) }}</span></td>
                <td>
                    @if($reservation->status === 'pendiente')
                        <form method="POST" action="{{ route('student.reservations.cancel', $reservation) }}">
                            @csrf @method('PATCH') <button class="danger-button" type="submit">Cancelar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
@endif
@endsection
