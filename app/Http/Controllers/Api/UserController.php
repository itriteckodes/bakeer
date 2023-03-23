<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Api;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Buyer;
use App\Models\Driver;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user(Request $request)
    {
        // dd($request->id);
        // $user=User::find($request->id);
        if (Auth::user()->type==1) {
            $user=Admin::find(Auth::user()->id);
            return Api::setResponse('user', $user);
        }
        elseif (Auth::user()->type==2) {
            $user=Seller::find(Auth::user()->id);
            return Api::setResponse('user', $user);
        }
        else{
            $user=Driver::find(Auth::user()->id);
            return Api::setResponse('user', $user);
        }
       
    } 

    public function search(Request $request){
        $sellers = Seller::where('first', 'LIKE', "%".$request->keyword."%");
        $sellers = $sellers->paginate(6)->getCollection();
        // $sellers->post= $sellers->post;
        $response = new Api();
        $response->add('sellers', $sellers);
        return $response->json();
    }
}
