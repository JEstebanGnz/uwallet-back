<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Google2FAController extends Controller
{
    public static function showValidate2FAView($user = null){
        //Es decir, si es un redirect desde la vista de google2FASetup
        return view('google2FAValidation');
    }


    public function validate2FA (Request $request){
        //Verify if token matches
//        $token2fa = $request->input('token2fa');

        $token2fa = $request->input('token_2fa');
        $user = DB::table('2fa_user_tokens')->select(['u.id'])
            ->where('token','=',$token2fa)
            ->join('users as u','2fa_user_tokens.user_id','=','u.id')
            ->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid token provided, try again'], 401);
        }


        $user = User::find($user->id);
        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user["google2fa_secret"], $request->input('otp'));

            // Revoke all tokens for the user if any previous exist
            $user->tokens()->delete();
            //And now create the session token
            $token = $user->createToken('auth_token')->plainTextToken;


            return response()->json(['user' => $user, 'token' => $token])->withHeaders(['auth-token' => $token]);


        return response()->json(['message' => 'The 6 digits are wrong'], 500);


    }
}
