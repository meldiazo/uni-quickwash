<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function reservations()
    {
        $reservations = Reservation::with(['student', 'machine'])
            ->orderByDesc('reservation_date')
            ->orderBy('schedule')
            ->get();

        return view('staff.reservations', compact('reservations'));
    }

    public function machines(Request $request)
    {
        $date = $request->query('date');
        $schedule = $request->query('schedule');
        $machines = Machine::orderBy('id')->get();

        return view('staff.machines', compact('machines', 'date', 'schedule'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Reservation::STATUSES)],
        ]);

        if (in_array($data['status'], Reservation::ACTIVE_STATUSES, true)) {
            $conflict = Reservation::query()
                ->where('id', '!=', $reservation->id)
                ->where('machine_id', $reservation->machine_id)
                ->whereDate('reservation_date', $reservation->reservation_date)
                ->where('schedule', $reservation->schedule)
                ->whereIn('status', Reservation::ACTIVE_STATUSES)
                ->exists();

            if ($conflict) {
                return back()->withErrors(['status' => 'No se puede aplicar ese estado: la máquina ya está ocupada en ese horario.']);
            }
        }

        $reservation->update(['status' => $data['status']]);

        return back()->with('success', 'Estado de la reserva actualizado.');
    }
}
