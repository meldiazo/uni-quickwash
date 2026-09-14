<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function isAvailable(?string $date, ?string $schedule): bool
    {
        if (! $date || ! $schedule) {
            return true;
        }

        return ! $this->reservations()
            ->whereDate('reservation_date', $date)
            ->where('schedule', $schedule)
            ->whereIn('status', Reservation::ACTIVE_STATUSES)
            ->exists();
    }
}
