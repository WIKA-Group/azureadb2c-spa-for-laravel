<?php

use WikaGroup\AzureAdB2cSpa\Http\Controllers\SsoLoginController;

Route::post('azureb2c/login', SsoLoginController::class)->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Session\Middleware\StartSession::class,
]);
