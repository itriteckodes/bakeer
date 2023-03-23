<?php

namespace App\Helpers;

class WithdrawStatus{

    public static function pending(){
        return 0;
    } 

    public static function completed(){
        return 1;
    } 
    
    public static function rejected(){
        return 2;
    } 
}