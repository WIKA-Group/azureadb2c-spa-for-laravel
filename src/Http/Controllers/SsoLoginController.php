<?php

namespace WikaGroup\AzureAdB2cSpa\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SsoLoginController
{
    public function __invoke(Request $request)
    {
        $email = $request->json('username');
        $name = $request->json('name');
        $oauthId = $request->json('localAccountId');

        if ($email === NULL || $name === NULL || $oauthId === NULL) {
            return Response::json(['msg' => 'Invalid request: Missing fields'], 400);
        }

        $oauthIdCol = config('azureadb2c.table.oauth_column');

        /** @var \App\Models\User $user */
        $user = User::where($oauthIdCol, $oauthId)
            ->firstOr(fn () => User::where('email', $email))->first();

        
        if (!$user->exists) {
            return Response::json(['msg' => 'User not found'], 404);
        }

        $user->$oauthIdCol = $oauthId;
        $user->name = $name;
        $user->save();

        Auth::loginUsingId($user->id);

        return json_encode($request->json(['msg' => 'OK', 'user' => ['name' => $name, 'email' => $email]]));
    }
}