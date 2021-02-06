<?php

use App\Http\Controllers\StripeSubscriptionController;
use App\Http\Controllers\User\SubscribedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('login'));
});


/*
|--------------------------------------------------------------------------
| User dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(static function (){

    Route::get('/dashboard', [StripeSubscriptionController::class, 'showPlanHTML'])->name('home');

    /**
     * Handle subscription
     */
    Route::post('/plan/subscribe', [StripeSubscriptionController::class, 'subscribeToPlan'])->name('postSubscribeToPlan');

});


/*
|--------------------------------------------------------------------------
| If user is subscribed - page is visible
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(static function(){
    Route::get('/secret-page', [SubscribedController::class,'secretPage'])->middleware('subscribed')->name('secret_page');
});


require __DIR__.'/auth.php';
