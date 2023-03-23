<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Transaction;
use App\Values\SetValue;

class TransactionHelper
{

    public static function userTransaction($user_id, $order)
    {
        Transaction::create([
            'user_id' => $user_id,
            'amount' => $order->grand_total,
            'nature' => 'Nill',
            'debit' => 0,
            'credit' => 0,
            'description' => 'An order #' . $order->id . ' of USD ' . $order->grand_total . ' was placed.',
        ]);
    }

    public static function makeAll($order)
    {

        $adminAccount = Account::find(1);
        $vendorAccount = $order->vendor->account;
        $riderAccount = $order->rider->account;
        
        $companycommission = ($order->subtotal * SetValue::CompanyCommission()) / 100;
        $ridercommission = SetValue::RiderCommission();
        $totalcommission = $companycommission + $ridercommission;

        Transaction::create([
            'account_id' => $vendorAccount->id,
            'opening' => $vendorAccount->balance,
            'closing' => $vendorAccount->balance + $order->grand_total,
            'amount' => $order->grand_total,
            'nature' => 'Credit',
            'debit' => 0,
            'credit' => $order->grand_total,
            'profit' => $order->grand_total,
            'description' =>  'USD ' . $order->grand_total . ' was added on order# ' . $order->id . '.',
        ]);

        $vendorAccount->update([
            'balance' => $vendorAccount->balance + $order->grand_total,
        ]);

        Transaction::create([
            'account_id' => $vendorAccount->id,
            'opening' => $vendorAccount->balance,
            'closing' => $vendorAccount->balance - $totalcommission,
            'amount' => $totalcommission,
            'nature' => 'Debit',
            'debit' => $totalcommission,
            'credit' => 0,
            'profit' => $totalcommission,
            'description' =>  'USD ' . $totalcommission . ' was deduct as Company Commission of order# ' . $order->id . '.',
        ]);

        $vendorAccount->update([
            'balance' => $vendorAccount->balance - $totalcommission,
        ]);

        Transaction::create([
            'account_id' => $adminAccount->id,
            'opening' => $adminAccount->balance,
            'closing' => $adminAccount->balance +  $companycommission,
            'amount' => $companycommission,
            'nature' => 'Credit',
            'debit' => 0,
            'credit' =>  $companycommission,
            'description' =>  'USD ' . $companycommission . ' was recieved on delivery of order# ' . $order->id . '.',
        ]);

        $adminAccount->update([
            'balance' => $adminAccount->balance + $companycommission,
        ]);

        Transaction::create([
            'account_id' => $riderAccount->id,
            'opening' => $riderAccount->balance,
            'closing' => $riderAccount->balance +  $ridercommission,
            'amount' => $ridercommission,
            'nature' => 'Credit',
            'debit' => 0,
            'credit' =>  $ridercommission,
            'description' =>  'USD ' . $ridercommission . ' was recieved as fare of order# ' . $order->id . '.',
        ]);

        $riderAccount->update([
            'balance' => $riderAccount->balance + $ridercommission,
        ]);
    
    
    }
    public static function withdraw($withdraw)
    
    {
    $account=$withdraw->owner->account;
        Transaction::create([
            'account_id' => $account->id,    
            'opening' => $account->balance,
            'closing' => $account->balance - $withdraw->amount,
            'amount' => $withdraw->amount,
            'nature' => 'Debit',
            'debit' => $withdraw->amount,
            'credit' => 0,
            'profit' => $withdraw->amount,
            'description' =>  'USD ' .$withdraw->amount . ' was witdrawn by ' . $withdraw->owner->name. '.',
        ]);

        $account->update([
            'balance' => $account->balance - $withdraw->amount,
        ]); 

    }
    public static function riderWithdraw($withdrawRequest)
    
    {
        $account = $withdrawRequest->owner->account;
        Transaction::create([
            'account_id' => $account->id,    
            'opening' => $account->balance,
            'closing' => $account->balance - $withdrawRequest->amount,
            'amount' => $withdrawRequest->amount,
            'nature' => 'Debit',
            'debit' => $withdrawRequest->amount,
            'credit' => 0,
            'profit' => $withdrawRequest->amount,
            'description' =>  'USD ' . $withdrawRequest->amount . ' was witdrawn by ' . $withdrawRequest->owner->name. '.',
        ]);

        $account->update([
            'balance' => $account->balance - $withdrawRequest->amount,
        ]); 

    }

}
