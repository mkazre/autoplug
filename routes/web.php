<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchServiceController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\GaragePhotoController;
use App\Http\Controllers\GarageQuoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:car_owner'])->prefix('quotes')->name('quotes.')->group(function () {
    Route::get('/', [QuoteRequestController::class, 'index'])->name('index');
    Route::post('/', [QuoteRequestController::class, 'store'])->name('store');
    Route::get('/{quoteRequest}', [QuoteRequestController::class, 'show'])->name('show');
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
    Route::get('requests', [GarageQuoteController::class, 'index'])->name('requests.index');
    Route::get('requests/{quoteRequestGarage}', [GarageQuoteController::class, 'show'])->name('requests.show');
    Route::post('requests/{quoteRequestGarage}/quote', [GarageQuoteController::class, 'storeQuote'])->name('requests.quote.store');
});

require __DIR__.'/auth.php';
