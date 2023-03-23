<?php

namespace App\Helpers;

class OrderStatus {

    public static function pending(){
        return 0;
    } 

    public static function accepted(){
        return 1;
    } 

    public static function dispatched(){
        return 2;
    }

    public static function picked(){
        return 3;
    }
   
    public static function delivered(){
        return 4;
    } 
    
    public static function rejected(){
        return 5;
    }
    
  
  

}