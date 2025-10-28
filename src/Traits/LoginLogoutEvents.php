<?php

namespace WikaGroup\AzureAdB2cSpa\Traits;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

trait LoginLogoutEvents
{
    #[On('azureb2c-login-succeeded')]
    public function azureB2cLoginSucceeded(array $user) {}

    #[On('azureb2c-login-failed')]
    public function azureB2cLoginFailed(string $msg)
    {
        Log::error('AzureAdB2cSpa - Login failed', ['msg' => $msg]);
    }

    #[On('azureb2c-logout-succeeded')]
    public function azureB2cLogoutSucceeded() {}

    #[On('azureb2c-logout-failed')]
    public function azureB2cLogoutFailed(string $msg)
    {
        Log::error('AzureAdB2cSpa - Logout failed', ['msg' => $msg]);
    }
}
