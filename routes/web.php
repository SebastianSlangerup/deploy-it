<?php

use App\Http\Controllers\InstanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [InstanceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('containers', [InstanceController::class, 'containers'])->name('containers.index');
    Route::get('servers', [InstanceController::class, 'servers'])->name('servers.index');

    Route::get('instances/{instance:id}', [InstanceController::class, 'view'])->name('instances.detail');
});

Route::get('/checkout', function (Request $request) {
    // Has to be routed to with a regular <a> tag and not Inertia's <Link> tag to prevent CORS errors
    return $request->user()
        ->newSubscription('default', 'price_1R6Xup5vdXkyZpPD70MRYQGx')
        ->checkout([
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancelled'),
        ]);
})->name('checkout');

Route::get('/checkout-success', function (Request $request) {
    return 'Thanks for your purchase!';
})->name('checkout.success');

Route::get('/checkout-cancelled', function (Request $request) {
    return 'Sorry to see you cancel!';
})->name('checkout.cancelled');

Route::get('/billing-portal', function (Request $request) {
    return Inertia::location($request->user()->redirectToBillingPortal());
})->middleware(['auth', 'verified'])->name('billing');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
