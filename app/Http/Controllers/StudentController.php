<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class StudentController extends Controller
{
    public const SCHEDULES = [
        '08:00 - 09:00',
        '09:00 - 10:00',
        '10:00 - 11:00',
        '11:00 - 12:00',
        '14:00 - 15:00',
        '15:00 - 16:00',
        '16:00 - 17:00',
    ];

    public function machines(Request $request)
    {
        $date = $request->query('date');
        $schedule = $request->query('schedule');

        $machines = Machine::orderBy('id')->get();

        return view('student.machines', compact('machines', 'date', 'schedule'));
    }

    public function reservations()
    {
        $reservations = auth()->user()->reservations()->with('machine')->latest('reservation_date')->latest()->get();
        $notifications = $this->buildNotifications($reservations);

        return view('student.reservations', compact('reservations', 'notifications'));
    }

    public function createReservation()
    {
        return view('student.reservation-create', [
            'machines' => Machine::orderBy('id')->get(),
            'schedules' => self::SCHEDULES,
        ]);
    }

    public function storeReservation(Request $request)
    {
        $data = $request->validate([
            'machine_id' => ['required', 'integer', 'exists:machines,id'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'schedule' => ['required', Rule::in(self::SCHEDULES)],
            'garment_count' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        try {
            DB::transaction(function () use ($data, $user) {
                $user = $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();

                if ($user->reservations()->whereIn('status', Reservation::ACTIVE_STATUSES)->count() >= 3) {
                    throw new \DomainException('Solo puedes tener hasta 3 reservas activas.');
                }

                $machine = Machine::whereKey($data['machine_id'])->lockForUpdate()->firstOrFail();

                if (! $machine->isAvailable($data['reservation_date'], $data['schedule'])) {
                    throw new \DomainException('La máquina ya está ocupada para esa fecha y horario.');
                }

                Reservation::create([
                    ...$data,
                    'user_id' => $user->id,
                    'status' => 'pendiente',
                ]);
            });
        } catch (Throwable $exception) {
            $message = $exception instanceof \DomainException
                ? $exception->getMessage()
                : 'No fue posible registrar la reserva. Verifica que la máquina siga disponible.';

            return back()->withErrors(['reservation' => $message])->withInput();
        }

        return redirect()->route('student.reservations')->with('success', 'Reserva registrada correctamente.');
    }

    public function cancelReservation(Reservation $reservation)
    {
        abort_unless($reservation->user_id === auth()->id(), 403);

        if ($reservation->status !== 'pendiente') {
            return back()->withErrors(['reservation' => 'Solo puedes cancelar reservas pendientes.']);
        }

        $reservation->update(['status' => 'cancelada']);

        return back()->with('success', 'Reserva cancelada.');
    }

    public function notifications()
    {
        $user = auth()->user();
        $reservations = $user->reservations()->with('machine')->whereIn('status', ['pendiente', 'en proceso', 'finalizada'])->get();

        return view('student.notifications', [
            'notifications' => $user->notifications_enabled ? $this->buildNotifications($reservations) : [],
            'enabled' => $user->notifications_enabled,
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $request->user()->update(['notifications_enabled' => $request->boolean('notifications_enabled')]);

        return back()->with('success', $request->boolean('notifications_enabled')
            ? 'Notificaciones habilitadas.'
            : 'Notificaciones deshabilitadas.');
    }

    private function buildNotifications($reservations): array
    {
        if (! auth()->user()->notifications_enabled) {
            return [];
        }

        $now = now();
        $until = now()->addDay();
        $notifications = [];

        foreach ($reservations as $reservation) {
            $startText = $reservation->reservation_date->format('Y-m-d').' '.Str::before($reservation->schedule, ' - ');
            $start = Carbon::createFromFormat('Y-m-d H:i', $startText);

            if ($reservation->status === 'pendiente' && $start->between($now, $until)) {
                $notifications[] = 'Tu reserva de '.$reservation->machine->name.' es próxima ('.$reservation->reservation_date->format('d/m/Y').' de '.$reservation->schedule.').';
            }

            if (in_array($reservation->status, ['en proceso', 'finalizada'], true)) {
                $notifications[] = $reservation->status === 'finalizada'
                    ? 'Tu ropa ya está lista en '.$reservation->machine->name.'. Recuerda recogerla.'
                    : 'Tu ropa está en proceso en '.$reservation->machine->name.'. Recuerda recogerla.';
            }
        }

        return $notifications;
    }
}
