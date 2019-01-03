<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use App\Exchanges;
use App\SubscribeMarket;
use Log;

class test extends Controller
{
    
    public function test(){
      
        $max_sub_mrkt =  Config::get('markets.sub_market_number');
         dd( url('/').'/image/call.jpg');
       $count = SubscribeMarket::where([['user_id','=', '2950844664941572'],['market_symbol','=', 'XMR/USD']])->count();
        dd($count);
        foreach ($kd as $key => $value) {
             dd($value);
        }

    }
}
