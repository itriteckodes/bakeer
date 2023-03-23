<?php

namespace App\Helpers;

class Gateway {

    public static function COD(){
        return 1;
    }

    public static function wallet(){
        return 2;
    }
    
    public static function CardPayment(){
        return 3;
    }

    public static function paypal(){
        return 4;
    }

    public static function jazzCash(){
        return 5;
    }

    public static function easyPaisa(){
        return 6;
    }

    public static function ericCoin(){
        return 7;
    }

    public static function jakCoin(){
        return 8;
    }
}