<?php 

use App\Helpers\Cart;
use App\Helpers\Gateway;
use App\Models\Wishlist;

function cartVendorId(){
    if(Cart::vendor())
        return Cart::vendor()->id;
    else
        return 'null';
}

function cartQty(){
    return Cart::qty();
}

function cartSubtotal(){
    return Cart::subTotal();
}



function cartShipping(){
    return Cart::shipping();
}

function cartDiscount(){
    return Cart::discount();
}

function cartGrandTotal(){
    return Cart::grandTotal();
}

function gateway(){
    return new Gateway();
}

function wishlistCount($id){
   return  Wishlist::where('user_id',$id)->count();
}

function sender($ticketReply){
    if ($ticketReply->type == 'rider')
    return $ticketReply->ticket->rider->name;
    else if($ticketReply->type == 'vendor')
    return $ticketReply->ticket->vendor->name;
    else if($ticketReply->type == 'user')
    return $ticketReply->ticket->user->name;
    else
    return $ticketReply->admin->name;  
}
