<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\FleetVehicleController;
use App\Http\Controllers\GarageBookingController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\GarageDirectoryController;
use App\Http\Controllers\GaragePhotoController;
use App\Http\Controllers\GarageQuoteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featured = \App\Models\Garage::approved()
        ->whereHas('branches', fn ($q) => $q->where('is_active', true))
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->with(['branches' => fn ($q) => $q->where('is_active', true), 'photos' => fn ($q) => $q->where('type', 'affiliation')])
        ->orderByDesc('reviews_avg_rating')
        ->take(6)
        ->get();

    $blocks = json_decode(\App\Support\Settings::get('home_blocks', '[]'), true) ?: [];

    return view('home', compact('featured', 'blocks'));
});

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/garages', [GarageDirectoryController::class, 'index'])->name('garages.index');
Route::get('/garages/{garage}', [GarageDirectoryController::class, 'show'])->name('garages.show');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/payfast/notify', [PaymentController::class, 'notify'])->name('payfast.notify');
Route::get('/payments/return', [PaymentController::class, 'return'])->name('payments.return');
Route::get('/payments/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/account/settings', [App\Http\Controllers\AccountController::class, 'edit'])->name('account.settings');
    Route::put('/account/settings', [App\Http\Controllers\AccountController::class, 'update'])->name('account.settings.update');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/go', [App\Http\Controllers\NotificationController::class, 'go'])->name('notifications.go');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.read-all');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin-export')->name('admin.export.')->group(function () {
    Route::get('users', [ExportController::class, 'users'])->name('users');
    Route::get('quote-requests', [ExportController::class, 'quoteRequests'])->name('quote-requests');
    Route::get('quotes', [ExportController::class, 'quotes'])->name('quotes');
    Route::get('bookings', [ExportController::class, 'bookings'])->name('bookings');
    Route::get('garages', [ExportController::class, 'garages'])->name('garages');
    Route::get('payments', [ExportController::class, 'payments'])->name('payments');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/platform-settings', [App\Http\Controllers\SettingsController::class, 'edit'])->name('platform.settings.edit');
    Route::put('/platform-settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('platform.settings.update');
});

Route::middleware(['auth', 'role:car_owner'])->group(function () {
    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/', [QuoteRequestController::class, 'index'])->name('index');
        Route::post('/', [QuoteRequestController::class, 'store'])->name('store');
        Route::get('/{quoteRequest}', [QuoteRequestController::class, 'show'])->name('show');
        Route::post('/{quoteRequest}/quotes/{quote}/accept', [QuoteRequestController::class, 'accept'])->name('accept');
    });

    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/pay', [PaymentController::class, 'pay'])->name('pay');
        Route::post('/{booking}/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('review');
        Route::get('/{booking}/invoice', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice');
    });

    Route::prefix('fleet')->name('fleet.')->group(function () {
        Route::get('/', [FleetController::class, 'dashboard'])->name('dashboard');
        Route::resource('vehicles', FleetVehicleController::class)->except(['show']);
        Route::get('drivers', [FleetController::class, 'drivers'])->name('drivers.index');
        Route::post('drivers', [FleetController::class, 'addDriver'])->name('drivers.store');
        Route::delete('drivers/{user}', [FleetController::class, 'removeDriver'])->name('drivers.destroy');
    });
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
    Route::get('bookings', [GarageBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [GarageBookingController::class, 'show'])->name('bookings.show');
    Route::post('bookings/{booking}/status', [GarageBookingController::class, 'updateStatus'])->name('bookings.status');
});

require __DIR__.'/auth.php';
