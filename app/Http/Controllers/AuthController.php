<?php

namespace App\Http\Controllers;

use App\Models\User;
use http\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;


class AuthController extends Controller
{
    public function redirectToGoogle(){
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->user();


        $authUser = User::firstOrCreate([
            'email' => $user->getEmail(),
        ], [
            'name' => $user->getName(),
            'password' => 'automatic_generate_password',
            'google_id' => $user->getId(),
            'google2fa_secret' => null,

        ]);

        // Log the user into your application
        Auth::login($authUser);

        // Revoke all tokens for the user if any previous exist
        $user = auth()->user();
        $user->tokens()->delete();

        //And now create the session token
        $token = $authUser->createToken('auth_token')->plainTextToken;
        $cookie = \Illuminate\Support\Facades\Cookie::make('access_token', $token, 60);

        return redirect()->away(Env('GOOGLE_SUCCESSFUL_LOGIN_REDIRECT'))->withCookie($cookie);
//        return response()->json(['data' => $authUser, 'access_token' => $token])->withCookie($cookie);
    }

    public function logout(Request $request){

        // Logout the user
        Auth::guard('web')->logout();
        // Revoke all tokens for the authenticated user
        $user = auth()->user();
        $user->tokens()->delete();

        return [
            'message' => 'You have successfully logged out'
        ];
    }

    public function userInfo(Request $request){

        return response()->json(['Message' => "You did it!"]);
    }

}
