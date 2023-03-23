<?php

namespace App\Helpers;

class RiderStatus {

    public static function pending(){
        return 0;
    } 
    public static function approved(){
        return 1;
    } 
    public static function rejected(){
        return 2;
    }
    
}