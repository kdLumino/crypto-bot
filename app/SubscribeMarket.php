<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscribeMarket extends Model
{
    protected $fillable = ['user_id', 'exchange_name', 'market_quoteid', 'market_baseid', 'market_symbol', 'market_price'];
}
