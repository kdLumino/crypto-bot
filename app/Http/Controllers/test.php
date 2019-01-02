<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use App\Exchanges;
use App\SubscribeMarket;

class test extends Controller
{
    
    public function test(){

           $max_sub_mrkt =  Config::get('markets.sub_market_number');
            $subscribe = SubscribeMarket::where('user_id', $recipientId)->get()->toArray();

            var_dump($max_sub_mrkt);
            dd($subscribe);

    }
}
