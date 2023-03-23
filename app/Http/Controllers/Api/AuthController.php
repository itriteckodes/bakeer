<?php

namespace App\Http\Controllers\Api;
use App\Helpers\Api;
use App\Helpers\ApiValidate;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Buyer;
use App\Models\Driver;
use App\Models\Seller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = ApiValidate::login($request, User::class);
        if (Auth::guard('user')->attempt($credentials)) { 
            $user = User::find(Auth::guard('user')->user()->id);
            return Api::setResponse('user', $user->withToken());
        } 
        else{
            return Api::setError('Invalid credentials');
        }
    }
    public function register(Request $request)
    {
        $credentials = ApiValidate::register($request, User::class);
        $user = User::create([
            'type' => $request->type
        ]+$credentials);
        if($request->type==1){
            $admin=Admin::create([
                'user_id' => $user->id
            ]+$credentials);
            return Api::setResponse('admin', $admin->withToken());
        }
        elseif($request->type==2){
            $seller=Seller::create([
                'user_id' => $user->id
            ]+$credentials);
            return Api::setResponse('seller', $seller->withToken());
        }else{
            $driver=Driver::create([
                'user_id' => $user->id
            ]+$credentials);
            return Api::setResponse('driver', $driver->withToken());   
        }
        return Api::setResponse('user', $user->withToken());
    }

    // public function sendForget(Request $request)
    // {
    //     $user = Driver::where('email',$request->email)->first();
        
    //     try{
    //         $user->code = Str::random(30);
    //         $user->save();
    //         Mail::send('mail.verification', ['user' => $user], function ($mail) use ($user){
    //             $mail->from('email', 'ShopeLive');
    //             $mail->to($user->email, $user->name)
    //             ->subject('Forget Password Verification');
    //         });

    //         return Api::setMessage('verification email sent');
    //     }
    //     catch(Exception $e){
    //         return Api::setError('Wrong email');
    //     }
    // }

    public function UpdateProfile(Request $request){
        $user =User::find(Auth::user()->id);
        $user->update($request->all());
        return Api::setResponse('user', $user);
    }
   
}
