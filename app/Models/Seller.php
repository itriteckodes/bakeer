<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use App\Traits\UserMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Seller extends Authenticatable
{
    use HasFactory, Notifiable, UserMethods;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'api_token',
        'phone',
        'type',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getImageAttribute($value){
        return asset($value);
    }


    public function setImageAttribute($value){
	    if(is_string($value)){
	        $this->attributes['image'] = ImageHelper::saveImageFromApi($value,'images/seller');
	    }
	    else if(is_file($value)){
	        $this->attributes['image'] = ImageHelper::saveResizedImage($value,'images/seller',650,700);
	    }
    }
    public function isSeller(){
        return $this->type == 2;
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
 
}

