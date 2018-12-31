<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


//custom chat bot 
Route::get("/callback", "fbbotcontroller@callback")->middleware("fbtoken");
Route::post("/callback", "fbbotcontroller@callback");



Route::get('/test', function()
{
//    header('Content-Type: application/json');
//    $kraken = new \ccxt\kraken([
//        'apiKey' => 'pk2FBIeEte9rR/PuoBhYB26exSpBi6xyXKBw/S44tJ5o6eAjngILjBzY',
//        'secret' => 'Xi7WUfmaIKfCMIg0lEUKM9xqET3Wv62gAmefHTqN0OOKaPhBSl4o+V5emr/dmys2w/tGgZUOVsN7lc3TQbckDw==',
//    ]);
//    dd(\ccxt\Exchange::$exchanges);
//$temparray = [];
    foreach (\ccxt\Exchange::$exchanges as $getExch)
    {
//        dump();

        $exchange_id = "kraken";
        $exchange_class = "\\ccxt\\$exchange_id";
        $exchange = new $exchange_class(array(
            'apiKey' => 'pk2FBIeEte9rR/PuoBhYB26exSpBi6xyXKBw/S44tJ5o6eAjngILjBzY',
            'secret' => 'Xi7WUfmaIKfCMIg0lEUKM9xqET3Wv62gAmefHTqN0OOKaPhBSl4o+V5emr/dmys2w/tGgZUOVsN7lc3TQbckDw==',
            'timeout' => 30000,
            'enableRateLimit' => true,
        ));
        dump($exchange->fetchMarkets());

//            $exchangeName = $getExch;
//            $temp['content_type'] = 'text';
//            $temp['title'] = $getExch;
//            $temp['payload'] = 'signal_name';
//            $temp['image_url'] = "https://via.placeholder.com/150";
//            
//            array_push($temparray,$temp);
    }

//        echo $jsonData = '{
//			    "recipient":{
//			        "id":"233454"
//			        },
//			        "message":{
//					    "text": "Pick Your Signal!",
//					    "quick_replies": '. json_encode($temparray).'
//					  }
//			    }'; 
////    dd($kraken->fetch_markets());
});
