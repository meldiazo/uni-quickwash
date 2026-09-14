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
        User::create([
            'name' => 'Personal QuickWash',
            'email' => 'personal@univalle.edu',
            'password' => 'personal123',
            'role' => 'personal',
            'notifications_enabled' => false,
        ]);

        $student = User::create([
            'name' => 'Estudiante de Prueba',
            'email' => 'estudiante@univalle.edu',
            'password' => 'estudiante123',
            'role' => 'estudiante',
            'notifications_enabled' => true,
        ]);

        $machines = [];
        foreach (['Máquina 1', 'Máquina 2', 'Máquina 3', 'Máquina 4'] as $name) {
            $machines[$name] = Machine::create(['name' => $name]);
        }

        Reservation::create([
            'user_id' => $student->id,
            'machine_id' => $machines['Máquina 1']->id,
            'reservation_date' => now()->addDay()->toDateString(),
            'schedule' => '08:00 - 09:00',
            'garment_count' => 5,
            'status' => 'pendiente',
        ]);

        Reservation::create([
            'user_id' => $student->id,
            'machine_id' => $machines['Máquina 2']->id,
            'reservation_date' => now()->toDateString(),
            'schedule' => '14:00 - 15:00',
            'garment_count' => 8,
            'status' => 'en proceso',
        ]);

        Reservation::create([
            'user_id' => $student->id,
            'machine_id' => $machines['Máquina 3']->id,
            'reservation_date' => now()->addDays(2)->toDateString(),
            'schedule' => '10:00 - 11:00',
            'garment_count' => 3,
            'status' => 'finalizada',
        ]);
    }
}
