<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Machine;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUser('personal', 'SEED_PERSONAL_EMAIL', 'SEED_PERSONAL_PASSWORD', false);
        $student = $this->seedUser('estudiante', 'SEED_STUDENT_EMAIL', 'SEED_STUDENT_PASSWORD', true);

        $machines = [];
        foreach (['Máquina 1', 'Máquina 2', 'Máquina 3', 'Máquina 4'] as $name) {
            $machines[$name] = Machine::firstOrCreate(['name' => $name]);
        }

        if (! $student) {
            return;
        }

        foreach ([
            [
                'machine_id' => $machines['Máquina 1']->id,
                'reservation_date' => now()->addDay()->toDateString(),
                'schedule' => '08:00 - 09:00',
                'garment_count' => 5,
                'status' => 'pendiente',
            ],
            [
                'machine_id' => $machines['Máquina 2']->id,
                'reservation_date' => now()->toDateString(),
                'schedule' => '14:00 - 15:00',
                'garment_count' => 8,
                'status' => 'en proceso',
            ],
            [
                'machine_id' => $machines['Máquina 3']->id,
                'reservation_date' => now()->addDays(2)->toDateString(),
                'schedule' => '10:00 - 11:00',
                'garment_count' => 3,
                'status' => 'finalizada',
            ],
        ] as $reservation) {
            $keys = [
                'user_id' => $student->id,
                'machine_id' => $reservation['machine_id'],
                'reservation_date' => $reservation['reservation_date'],
                'schedule' => $reservation['schedule'],
            ];

            $exists = Reservation::query()
                ->where('user_id', $keys['user_id'])
                ->where('machine_id', $keys['machine_id'])
                ->whereDate('reservation_date', $keys['reservation_date'])
                ->where('schedule', $keys['schedule'])
                ->exists();

            if (! $exists) {
                Reservation::create([
                    ...$keys,
                    'garment_count' => $reservation['garment_count'],
                    'status' => $reservation['status'],
                ]);
            }
        }
    }

    private function seedUser(string $role, string $emailKey, string $passwordKey, bool $notificationsEnabled): ?User
    {
        $email = env($emailKey, $role === 'personal'
            ? 'personal@univalle.edu'
            : 'estudiante@univalle.edu');
        $password = env($passwordKey, $role === 'personal'
            ? 'personal123'
            : 'estudiante123');

        if (! $email || ! $password) {
            return null;
        }

        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $role === 'personal' ? 'Personal QuickWash' : 'Estudiante de Prueba',
                'password' => $password,
                'role' => $role,
                'notifications_enabled' => $notificationsEnabled,
            ],
        );
    }
}
