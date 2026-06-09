<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchServiceController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\GaragePhotoController;
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
    Route::get('branches/{branch}/services', [BranchServiceController::class, 'index'])->name('branches.services.index');
    Route::post('branches/{branch}/services', [BranchServiceController::class, 'store'])->name('branches.services.store');
    Route::put('branches/{branch}/services/{garageService}', [BranchServiceController::class, 'update'])->name('branches.services.update');
    Route::delete('branches/{branch}/services/{garageService}', [BranchServiceController::class, 'destroy'])->name('branches.services.destroy');
    Route::get('photos', [GaragePhotoController::class, 'index'])->name('photos.index');
    Route::post('photos', [GaragePhotoController::class, 'store'])->name('photos.store');
    Route::delete('photos/{photo}', [GaragePhotoController::class, 'destroy'])->name('photos.destroy');
});

require __DIR__.'/auth.php';
