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
                        $recipientId = 2200772469954510;
                     $subscribe = SubscribeMarket::where('user_id', $recipientId)->get()->toArray();
				$temparray = [];
				if($subscribe){
					foreach ($subscribe as $key => $value) {
					$temp['title'] = 'Your Selected Exchnage is '.$value['exchange_name'].' and its Market symbol is '.$value['market_symbol'].' ';
					$temp['subtitle'] = 'Your Market Last Price is '.$value['market_price'].' ';
					$btn['button']['type'] = "postback";
					$btn['button']['title'] = 'UnSubscribe Market';
					$btn['button']['payload'] = $value['market_symbol'];
					$button=[];
					array_push($button,$btn['button']);
					//array_push(,$button);
					$temp['buttons'] = $button;
					array_push($temparray,$temp);
					}
				}
			
			    $jsonData = '{
					    "recipient":{
					        "id":"' . $recipientId . '"
					        },
					      "message": {
						    "attachment": {
						        "type": "template",
						        "payload": {
						            "template_type": "list",
						            "top_element_style": "compact",
						            "elements": '.json_encode($temparray).'
						        }
						    }
						}
                    }';

                    {
                        "recipient":{
                            "id":"2200772469954510"
                        },
                        "message": {
                            "attachment": {
                                "type": "template",
                                "payload": {
                                    "template_type": "list",
                                    "top_element_style": "compact",
                                    "elements": [
                                        {
                                        "title":"Your Selected Exchnage is kraken and its Market symbol is ADA/ETH ",
                                        "subtitle":"Your Market Last Price is 0.00029",
                                            "buttons":[
                                                {
                                                "type":"postback",
                                                "title":"UnSubscribe Market",
                                                "payload":"ADA/ETH"
                                                }
                                            ]
                                        }
                                        {
                                        "title":"Your Selected Exchnage is kraken and its Market symbol is ADA/ETH ",
                                        "subtitle":"Your Market Last Price is 0.00029",
                                            "buttons":[
                                                {
                                                "type":"postback",
                                                "title":"UnSubscribe Market",
                                                "payload":"ADA/ETH"
                                                }
                                            ]
                                        }
                                    ]
                                    
                                }
                            }
                        }   
                    }"

                    dd(json_encode($jsonData));
                    
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
