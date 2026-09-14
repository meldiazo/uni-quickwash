<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect(auth()->user()->isStaff() ? route('staff.reservations') : route('student.machines'))
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:estudiante'])->prefix('estudiante')->name('student.')->group(function () {
    Route::get('/maquinas', [StudentController::class, 'machines'])->name('machines');
    Route::get('/reservas', [StudentController::class, 'reservations'])->name('reservations');
    Route::get('/reservas/nueva', [StudentController::class, 'createReservation'])->name('reservations.create');
    Route::post('/reservas', [StudentController::class, 'storeReservation'])->name('reservations.store');
    Route::patch('/reservas/{reservation}/cancelar', [StudentController::class, 'cancelReservation'])->name('reservations.cancel');
    Route::get('/notificaciones', [StudentController::class, 'notifications'])->name('notifications');
    Route::patch('/notificaciones', [StudentController::class, 'updateNotifications'])->name('notifications.update');
});

Route::middleware(['auth', 'role:personal'])->prefix('personal')->name('staff.')->group(function () {
    Route::get('/maquinas', [StaffController::class, 'machines'])->name('machines');
    Route::get('/reservas', [StaffController::class, 'reservations'])->name('reservations');
    Route::patch('/reservas/{reservation}/estado', [StaffController::class, 'updateStatus'])->name('reservations.status');
});
