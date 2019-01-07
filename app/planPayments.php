<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class planPayments extends Model
{
    protected $fillable = ['auth_user_id', 'transaction_id', 'payment_id', 'transaction_price', 'plan_name', 'payment_method'];
}
