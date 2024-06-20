<?php

namespace App\Http\Controllers;

use App\Models\Google2FA;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function redirectToGoogle(){
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'password' => 'automatic_generate_password',
            'google2fa_secret' => null,
        ]);

        //Set the 2FA Token to user
        $tokens = User::setAndGetUser2FATokens($user);
        $setupSecretKey = $tokens['setupSecretKey'];
        $token_2fa = $tokens['token_2fa'];

        $setupSecretKeyCookie = Cookie::make('setup_secret_key', $setupSecretKey, 60);
        $token_2faCookie = Cookie::make('token_2fa', $token_2fa, 60);
        return redirect()->away(Env('GOOGLE_SUCCESSFUL_LOGIN_REDIRECT'))->withCookies([$setupSecretKeyCookie, $token_2faCookie ]);


//        Auth::login($user);
        $user->tokens()->delete();
        //And now create the session token
        $token = $user->createToken('auth_token')->plainTextToken;
        $cookie = Cookie::make('access_token', $token, 60);




        return redirect()->away(Env('GOOGLE_SUCCESSFUL_LOGIN_REDIRECT'))->withCookie($cookie);


        $firstLogin = User::handleUserStatusRedirect($user);

        if ($firstLogin){
//            Auth::login($user);
            $google2fa = app('pragmarx.google2fa');
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
            Session::put('user',$user);
            return view('google2FASetup', ['secret' => $secret]);
        }


    }

    public function logout(Request $request){

        // Logout the user
        Auth::guard('web')->logout();
        // Revoke all tokens for the authenticated user
        $user = auth()->user();
        $user->tokens()->delete();
        Cookie::queue(Cookie::forget('access_token'));
//        \Illuminate\Support\Facades\Cookie::make('access_token', $token, 60);

        return [
            'message' => 'You have successfully logged out'
        ];
    }

    public function userInfo(Request $request){
        return response()->json(['Message' => "You did it!"]);
    }

}
