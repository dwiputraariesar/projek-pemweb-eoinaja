<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Stripe webhook is exposed here (no CSRF) so Stripe can POST events to it.
|
*/

Route::post('webhooks/stripe', [WebhookController::class, 'handle']);
