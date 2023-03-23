<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Validate{

    public static function login($request, $model){

        $validator = Validator::make($request->all(),$model::loginRules());
        
        if($validator->fails()){
            toastr()->error($validator->errors()->first());
            
            if(Request::is('/vendor')){
                Redirect::to(route('front.vendor.login'))->withInput()->send();

            }else{
                Redirect::to(route('user.login'))->withInput()->send();

            }
        }
        else
            return[
                'email' => $request->email,
                'password' => $request->password
            ];
    }
    
    public static function register($request, $model, $addVerification = false){
        if(Request::is('vendor/*')){
        $validator = Validator::make($request->all(),$model::VendorRegisterRules());
        }else{
        $validator = Validator::make($request->all(),$model::registerRules());

        }
        
        if($validator->fails()){
            toastr()->error($validator->errors()->first());
            if(Request::is('vendor/*')){
                Redirect::to(route('front.vendor.register'))->withInput()->send();
            }else{
                Redirect::to(route('user.register'))->withInput()->send();
            }
           
        }
        else{
            $data = [ 'api_token' => Str::random(60) ] + $request->all();
            if($addVerification) $data['email_verify_code'] = Str::random(20);
            return $data;
        }
          
    }



}