<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google2fa_secret',
        'google2fa_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function handleUserStatusRedirect(User $user){
        // Check if user already has 2FA enabled.
        if (!$user["google2fa_secret"] || !$user["google2fa_enabled"]){
            return true;
//            return Google2FA::setup2FA($user);
        }

        return false;
//        return Google2FA::showValidate2FAView($user); // Redirect to 2FA validation
    }

    public static function setUserLoginToken ($user){
        $token_2fa = bin2hex(random_bytes(10));
        DB::table('2fa_user_tokens')
            ->updateOrInsert(['user_id'=> $user['id']], ['token' => $token_2fa, 'expiration' => 45]);
        return $token_2fa;
    }

    public static function setGoogle2FASecretKey ($user){
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $user->google2fa_secret = $secret;
        $user->save();
        return $secret;
    }

    public static function setGoogle2FAAuthEnabled($user){

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

    public static function validateGoogleOTPCode($user, $otpCode){
        $google2fa = app('pragmarx.google2fa');
        return $google2fa->verifyKey($user["google2fa_secret"], $otpCode);
    }

    public static function validateLoginToken($token2FA){
        return DB::table('2fa_user_tokens')->select(['u.id'])
            ->where('token','=',$token2FA)
            ->join('users as u','2fa_user_tokens.user_id','=','u.id')
            ->first();
    }



}
