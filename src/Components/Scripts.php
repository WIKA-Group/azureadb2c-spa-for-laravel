<?php

namespace WikaGroup\AzureAdB2cSpa\Components;

use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Scripts extends \Livewire\Component
{
    // MARK: Event listeners

    #[On('azureb2c-logout')]
    public function logoutEvent()
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $this->js('window.msalConfigIsAlreadyLoggedIn = ' . (Auth::check() ? 'true' : 'false'));
        $this->dispatch('azureb2c-logout-succeeded');
    }

    #[On('azureb2c-login')]
    public function loginEvent($token)
    {
        $userData = $this->verifyToken($token);
        if ($userData === null) {
            $this->dispatch('azureb2c-login-failed', msg: 'Invalid token');

            return;
        }

        $email = $userData['email'];
        $name = $userData['name'];
        $oauthId = $userData['sub'];

        if ($email === null || $name === null || $oauthId === null) {
            $this->dispatch('azureb2c-login-failed', msg: 'Invalid request: Missing fields in token');

            return;
        }

        $oauthIdCol = config('azureadb2c.table.oauth_column');

        try {
            /** @var \App\Models\User $user */
            $user = User::where($oauthIdCol, $oauthId)->first()
                ?? User::where('email', $email)->first()
                ?? new User;
    
            $user->$oauthIdCol = $oauthId;
            $user->email = $email;
            $user->name = $name;
            $user->password = '';
            $user->save();
        } catch (\Throwable $th) {
            $this->dispatch('azureb2c-login-failed', msg: 'Failed to create or update user: ' . $th->getMessage());

            return;
        }

        if (Auth::loginUsingId($user->id) === false) {
            $this->dispatch('azureb2c-login-failed', msg: 'Invalid request: Failed to login with user');

            return;
        }

        $this->js('window.msalConfigIsAlreadyLoggedIn = ' . (Auth::check() ? 'true' : 'false'));
        $this->dispatch('azureb2c-login-succeeded', user: $userData);
    }

    // MARK: Main functions

    public function render(): \Illuminate\View\View
    {
        return view('azureadb2c::components.scripts');
    }

    // MARK: Helper functions

    private function verifyToken(string $token): ?array
    {
        $domain = config('azureadb2c.connection.custom_domain') ?? config('azureadb2c.connection.domain') . '.b2clogin.com';
        $tenant = config('azureadb2c.connection.domain');
        $policy = config('azureadb2c.connection.policy');
        $jwksUrl = "https://$domain/$tenant.onmicrosoft.com/discovery/v2.0/keys?p=$policy";

        try {
            // Fetch public keys
            $jwks = json_decode(file_get_contents($jwksUrl), true);

            // Decode and verify token
            $decoded = JWT::decode($token, JWK::parseKeySet($jwks, config('azureadb2c.connection.default_algorithm')));

            // Optional: Validate claims
            if ($decoded->aud !== config('azureadb2c.connection.client_id')) {
                Log::error('AzureAdB2cSpa: Invalid audience');

                return null;
            }

            if (!in_array($decoded->iss, $this->getIssuerDomains(), true)) {
                Log::error('AzureAdB2cSpa: Invalid issuer', ['issuer' => $decoded->iss]);

                return null;
            }

            return (array) $decoded;
        } catch (\Exception $e) {
            Log::error('AzureAdB2cSpa: Token verification failed', ['ex' => $e->getMessage()]);

            return null;
        }
    }

    private function getIssuerDomains(): array
    {
        $customDomain = config('azureadb2c.connection.custom_domain');
        $domain = config('azureadb2c.connection.domain');
        $tenantId = config('azureadb2c.connection.tenant_id');

        $issuers = [];
        if ($domain) {
            $issuers[] = "https://$domain.b2clogin.com/$domain.onmicrosoft.com/v2.0/";
        }
        if ($customDomain && $domain) {
            $issuers[] = "https://$customDomain/$domain.onmicrosoft.com/v2.0/";
        }
        if ($customDomain && $tenantId) {
            $issuers[] = "https://$customDomain/$tenantId/v2.0/";
        }
        if ($domain && $tenantId) {
            $issuers[] = "https://$domain.b2clogin.com/$tenantId/v2.0/";
        }

        return $issuers;
    }
}
