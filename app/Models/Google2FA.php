<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Google2FA extends Model
{
    public static function setup2FA(User $user)
    {
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $user->google2fa_secret = $secret;
        $user->save();
        Session::put('temporary_user', $user);
        return view('google2FASetup', ['secret' => $secret]);
    }

    public static function showValidate2FAView($user = null){

        //Es decir, si es un redirect desde la vista de google2FASetup
        if($user === null){
            $user = Session::get('temporary_user');
        }
        if($user->google2fa_enabled === false){
            $user->google2fa_enabled = true;
            $user->save();
        }
        Session::put('temporary_user', $user);
        return view('google2FAValidation');
    }

    use HasFactory;
}
