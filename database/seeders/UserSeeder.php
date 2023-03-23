<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(
            [
            'first' => 'Buyer','email' => 'buyer@mail.com','type'=>1,'password' => 123456,
            'api_token' => 'W4hXQoNmr3P2kihZTem2btsvwgBU1jO6LbGLp857Wk4NfXReO40ak6jRGjFl'
            ],
        [
            'first' => 'Seller','email' => 'seller@mail.com','type'=>2,'password' => 123456,
            'api_token' => 'UsErrl3lDsXkh9YMbDPkv0Abdz7Vgppc9Oq239GSln9noNJEn4beAdNYPQzvee'
        ]
    );

       
    }
}
