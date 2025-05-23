<?php

use App\Http\Controllers\InstanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [InstanceController::class, 'index'])
    ->middleware(['auth', 'verified', 'subscribed'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'admin', 'subscribed'])->group(function () {
    Route::get('containers', [InstanceController::class, 'containers'])->name('containers.index');
    Route::get('servers', [InstanceController::class, 'servers'])->name('servers.index');

    Route::get('instances/{instance:id}', [InstanceController::class, 'show'])->name('instances.show');
    Route::get('instances/create/{instance_type}', [InstanceController::class, 'create'])->name('instances.create');
    Route::get('instances/create-container/{instance:id}', [InstanceController::class, 'createContainer'])->name('instances.create.container');
    Route::post('instances/store', [InstanceController::class, 'store'])->name('instances.store');
    Route::post('instances/container/store/{instance:id}', [InstanceController::class, 'storeContainer'])->name('instances.container.store');
    Route::patch('instances/{instance:id}/rename', [InstanceController::class, 'rename'])->name('instances.rename');
    Route::delete('instances/{instance:id}', [InstanceController::class, 'destroy'])->name('instances.destroy');

    Route::post('instances/{instance:id}/action', [InstanceController::class, 'performAction'])->name('instances.action');
});

Route::get('/checkout', function (Request $request) {
    // Has to be routed to with a regular <a> tag and not Inertia's <Link> tag to prevent CORS errors
    $checkout = $request->user()
        ->newSubscription('default', 'price_1R6Xup5vdXkyZpPD70MRYQGx')
        ->checkout([
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancelled'),
        ]);

    return Inertia::location($checkout->asStripeCheckoutSession()->url);
})->middleware(['auth', 'verified'])->name('checkout');

Route::get('/checkout-success', function (Request $request) {
    return 'Thanks for your purchase!';
})->name('checkout.success');

Route::get('/checkout-cancelled', function (Request $request) {
    return 'Sorry to see you cancel!';
})->name('checkout.cancelled');

Route::get('/billing-portal', function (Request $request) {
    return $request->user()->redirectToBillingPortal(route('dashboard'));
})->middleware(['auth', 'verified', 'subscribed'])->name('billing');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
