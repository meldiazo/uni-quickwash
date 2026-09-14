<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public const ACTIVE_STATUSES = ['pendiente', 'en proceso'];
    public const STATUSES = ['pendiente', 'en proceso', 'finalizada', 'cancelada'];

    protected $fillable = [
        'user_id',
        'machine_id',
        'reservation_date',
        'schedule',
        'garment_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'garment_count' => 'integer',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }
}
