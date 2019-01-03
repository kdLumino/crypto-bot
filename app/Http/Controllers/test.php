<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use App\Exchanges;
use App\SubscribeMarket;
use Log;

class test extends Controller
{
    public function kd(){
       
    }
    public function test(){

        $t=time();

        $exchange_id = 'kraken';

        $exchange_class = '\\ccxt\\' . $exchange_id;
        $exchange = new $exchange_class ();
        $markets = $exchange->fetchTickers();
        foreach ($markets as $key => $value)
        {	
            if( $value['symbol'] == 'BTC/GBP' ){
            // dd($value);   	 
            }
        }

        // if ($exchange->has['fetchTrades']) {
        //     foreach ($exchange->fetch_trades('XMR/USD') as $key => $value) {
        //         usleep ($exchange->rateLimit * 1000); // usleep wants microseconds
        //         // dd($value);
        //         if($value['timestamp'] == $t){
        //             // dd($value);
        //         }
                  
        //     }
        // }

        if ($exchange->has['fetchOHLCV'] = 'emulated') {

        $markets = $exchange->fetchTickers();
          usleep ($exchange->rateLimit * 1000); // usleep wants microseconds
            dd ($exchange->fetch_ohlcv('XMR/USD'));  
        }

    }
}
