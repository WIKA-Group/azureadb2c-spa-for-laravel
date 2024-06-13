<?php

use WikaGroup\AzureAdB2cSpa\Http\Controllers\SsoLoginController;

Route::post('azureb2c/login', SsoLoginController::class);