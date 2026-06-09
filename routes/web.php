<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:garage_owner'])->prefix('garage')->name('garage.')->group(function () {
    Route::get('/', [GarageController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [GarageController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [GarageController::class, 'updateProfile'])->name('profile.update');
    Route::resource('branches', BranchController::class)->except(['show']);
});

require __DIR__.'/auth.php';
