<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable=['account_number','user_id','amount'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
