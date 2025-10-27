<?php

namespace WikaGroup\AzureAdB2cSpa\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class SsoLoginController
{
    public function __invoke(\Illuminate\Http\Request $request)
    {
        $userData = $this->verifyToken($request->json('idToken'));
        if ($userData === null) {
            return Response::json(['msg' => 'Invalid token'], 400);
        }

        $email = $userData['email'];
        $name = $userData['name'];
        $oauthId = $userData['sub'];

        if ($email === null || $name === null || $oauthId === null) {
            return Response::json(['msg' => 'Invalid request: Missing fields'], 400);
        }

        $oauthIdCol = config('azureadb2c.table.oauth_column');

        /** @var \App\Models\User $user */
        $user = User::where($oauthIdCol, $oauthId)
            ->firstOr(fn () => User::where('email', $email))->firstOrNew();

        $user->$oauthIdCol = $oauthId;
        $user->email = $email;
        $user->name = $name;
        $user->password = '';
        $user->save();

        if (Auth::loginUsingId($user->id) === false) {
            return Response::json(['msg' => 'Failed to login with user'], 400);
        }

        return Response::json(['msg' => 'OK', 'user' => ['name' => $name, 'email' => $email]]);
    }

    private function verifyToken(string $token): ?array
    {
        if (empty($token)) {
            return null;
        }

        $domain = config('azureadb2c.connection.custom_domain') ?? config('azureadb2c.connection.domain') . '.b2clogin.com';
        $tenant = config('azureadb2c.connection.domain');
        $policy = config('azureadb2c.connection.policy');

        try {
            // Fetch public keys
            $jwksUrl = "https://$domain/$tenant.onmicrosoft.com/discovery/v2.0/keys?p=$policy";
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
