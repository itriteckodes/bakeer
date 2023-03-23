<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class MailHelper{
    public static function sendInvoice($receiver, $msg){
        $data = ['msg' => $msg];
        Mail::send('mail.invoice', $data, function ($message) use ($receiver){
            $message->from('opasco@support.com', 'Opasco');
            $message->to($receiver->email, $receiver->name)
            ->subject('Invoice');
        });
    }

    public static function sendInvoiceToSeller($receiver, $msg){
        $data = ['msg' => $msg];
        Mail::send('mail.invoice', $data, function ($message) use ($receiver){
            $message->from('opasco@support.com', 'Opasco');
            $message->to($receiver->seller->email, $receiver->name)
            ->subject('Invoice');
        });
    }
    public static function sendInvoiceToAgent($receiver, $msg){
        $data = ['msg' => $msg];
        Mail::send('mail.invoice', $data, function ($message) use ($receiver){
            $message->from('opasco@support.com', 'Opasco');
            $message->to($receiver->agent->email, $receiver->name)
            ->subject('Invoice');
        });
    }

    public static function SendEmailCode($receiver){
        $data = ['receiver' => $receiver];

        Mail::send('mail.email_verify', $data, function ($message) use ($receiver){
            $message->from('jakbazar@support.com', 'JAkBazar');
            $message->to($receiver->email, $receiver->name)
            ->subject('Email Verify');
        });
    }

}