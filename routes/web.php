<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

//Route::get('/auth/google/redirect', function () {
//    return \Laravel\Socialite\Facades\Socialite::driver("google")->stateless()->redirect();
//});
//
//
//Route::get('/auth/google/callback', function (\Illuminate\Http\Request $request){
//    $user = Socialite::driver('google')->stateless()->user();
//
//    $authUser = User::firstOrCreate([
//        'email' => $user->getEmail(),
//    ], [
//        'name' => $user->getName(),
//        'password' => 'automatic_generate_password',
//        'google_id' => $user->getId(),
//        'google2fa_secret' => null,
//    ]);
//
//    // Log the user into your application
//    Auth::login($authUser);
//
//    $token = $user->createToken('auth_token')->plainTextToken;
//
//    return response()->json(['data' => $authUser, 'access_token' => $token , 'token_type' => 'Bearer',]);
//});


require __DIR__.'/auth.php';
