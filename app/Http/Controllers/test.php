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
            $subscribe = SubscribeMarket::where('user_id', '2950844664941572')->get()->toArray();

           if( count($subscribe) <= $max_sub_mrkt[0]){
               dd($max_sub_mrkt);
            }else{
                dd('dfdf');
            }

    }
}
