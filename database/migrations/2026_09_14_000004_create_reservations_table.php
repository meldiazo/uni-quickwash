<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->date('reservation_date');
            $table->string('schedule');
            $table->unsignedInteger('garment_count');
            $table->string('status')->default('pendiente');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['reservation_date', 'schedule', 'status']);
        });

        DB::statement("CREATE UNIQUE INDEX reservations_active_machine_slot_unique
            ON reservations (machine_id, reservation_date, schedule)
            WHERE status IN ('pendiente', 'en proceso')");
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
