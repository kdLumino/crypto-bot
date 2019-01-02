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
           \Log::info($max_sub_mrkt[0]);
       

    }
}
