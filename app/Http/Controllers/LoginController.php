<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    protected $headers = array('Content-Type' => 'application/json; charset=utf-8');

    public function login()
    {
        return response('please log in');
    }

    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->user();
        } catch (\Throwable $th) {
            return response()->json([
                'error' => '登入失敗',
            ], 422, $this->headers, JSON_UNESCAPED_UNICODE);
        }
        $userCreated = User::firstOrCreate([
            'email' => $user->getEmail(),
        ], [
            'email_verified_at' => now(),
            'name' => $user->getName(),
        ]);
        $userCreated->providers()->updateOrCreate([
            'provider' => $provider,
            'provider_id' => $user->getId(),
        ], [
            //extra info
        ]);
        Auth::login($userCreated);
        // generate token
        $token = $userCreated->createToken('token-name')->plainTextToken;

        return response()->json($userCreated, 200, ['Access-Token' => $token]);
    }

    protected function validateProvider($provider)
    {
        $allowedProvider = ['facebook'];
        if (!in_array($provider, $allowedProvider)) {
            return response()->json([
                'error' => '請用fb登入',
            ], 422, $this->headers, JSON_UNESCAPED_UNICODE);
        }
    }
}
