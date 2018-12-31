<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use view;
use App\Exchanges;
use App\SubscribeMarket;
use Cache;

class fbbotcontroller extends Controller
{

 public function callback(Request $request){
        $data = $request->all();
        
    	$kd = json_encode($data['entry'][0]['changes'][0]['field']);
    	file_put_contents( "php://stderr","$kd");

    	$marketsarr = $this->fetchMarketBaseQuote('Kraken');
    
	        
	        $payload = $data['entry'][0]['messaging'][0];
	        $id      = $data["entry"][0]["messaging"][0]["sender"]["id"];

	        if( !empty($payload) ){
	        	if( !empty($payload['postback']['payload']) ){
		        	if($payload['postback']['payload'] == 'get'){
		        		$this->defaultTextMessage($id, $payload['postback']['payload']);
		        	}else if($payload['postback']['payload'] == 'get_exchange'){
		        		$this->exchangeTextMessage($id, $payload['postback']['payload']);
		        	}else if($payload['postback']['payload'] == 'subscribe_list'){
		        		$this->exchangeTextMessage($id, $payload['postback']['payload']);
		        	}else{
		        		$this->unSubscribeMarketTextMessage($id, $payload['postback']['payload']);
		        	}
	        	}else if(!empty($payload['message']['quick_reply'])) {
	        		if($payload['message']['quick_reply']['payload'] == 'market_subscribe'){
	        			$this->selectMarketMessage($id, $payload['message']['quick_reply']['payload']);
	        		}else if($payload['message']['quick_reply']['payload'] == 'no_subscribe'){
	        			$this->selectMarketMessage($id, $payload['message']['quick_reply']['payload']);
	        		}else if( $payload['message']['quick_reply']['payload'] == 'start_default'){
	        			$this->defaultTextMessage($id, $payload['postback']['payload']);
	        		}else{
	                  $this->marketTextMessage($id, $payload['message']['quick_reply']['payload']);
	        		}
	        	}else{
	        		
	        		if (Cache::has('marketBaseQuote')) {
						   $senderMessage = $data["entry"][0]["messaging"][0]['message'];
	        	           $this->marketBaseCurrency($id, $senderMessage['text']);
						}else{
						  $senderMessage = $data["entry"][0]["messaging"][0]['message'];
	        	          $this->sendWelcomeMessage($id, $senderMessage['text']);
						}
	        	}
	        }
	        $this->getGrettingText();
	        $this->getStarted();  
    }
    private function getUserDetails($recipientId){
    	$ch = curl_init('https://graph.facebook.com/'.$recipientId.'?access_token='. env("PAGE_ACCESS_TOKEN"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    private function getGrettingText(){
		$url = 'https://graph.facebook.com/v3.2/me/messenger_profile?access_token=' . env("PAGE_ACCESS_TOKEN");
		/*initialize curl*/
		$ch = curl_init($url);
		/*prepare response*/
		$jsonData = '{
			  "greeting":[
				  {
				    "locale":"default",
				    "text":"Hello dsdsdsdsds!"
				  }
				]
			}';
				/* curl setting to send a json post data */
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_exec($ch);
		curl_close($ch);
    }
    private function getStarted(){
		$url = 'https://graph.facebook.com/v3.2/me/messenger_profile?access_token=' . env("PAGE_ACCESS_TOKEN");
		/*initialize curl*/
		$ch = curl_init($url);
		/*prepare response*/
		$jsonData = '{
		  "get_started": {"payload": "get"}
		}';
				/* curl setting to send a json post data */
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_exec($ch);
		curl_close($ch);
    }
    private function sendAction($recipientId){
    	$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		/*initialize curl*/
		$ch = curl_init($url);
	    /*prepare response*/
	    $jsonData = '{
	    "recipient":{
	        "id":"' . $recipientId . '"
	        },
	        "sender_action":"typing_on"
	    }';
    	/* curl setting to send a json post data */
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	    curl_exec($ch);
        curl_close($ch);
    }
    private function defaultTextMessage($recipientId, $messageText){
    	$this->sendAction($recipientId);
    	Cache::pull('marketBaseQuote');
        $user = $this->getUserDetails($recipientId);
		$userdata = json_decode($user);
		
    	$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		    /*initialize curl*/
		    $ch = curl_init($url);
	       		       /*prepare response*/
			    $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":{
				    "attachment":{
				      "type":"template",
				      "payload":{
				        "template_type":"generic",
				        "elements":[
				           {
				            "title":"Hey ' . $userdata->first_name . ' Good To see You.!",
				            "image_url":"https://lz-bot.herokuapp.com/image/bitcoin-falling-760x400.jpg",
				            "subtitle":"We have the right hat for everyone.",
				            "default_action": {
				              "type": "web_url",
				              "url": "https://lz-bot.herokuapp.com",
				              "webview_height_ratio": "tall",
				            },
				            "buttons":[
				              {
				                "type":"postback",
				                "title":"Pick Our Exchanges!",
				                "payload":"get_exchange"
				              }              
				            ]      
				          }
				        ]
				      }
				    }
				  }
			    }';
	       
	        /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    private function exchangeTextMessage($recipientId, $messageText){
        $user = $this->getUserDetails($recipientId);
		$userdata = json_decode($user);
	
    	$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		    /*initialize curl*/
		    $ch = curl_init($url);
			/* prepare response */
			if( $messageText == 'subscribe_list'){
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
			}else{
			    $temparray = [];
			    $exchanges = Exchanges::select()->get()->toArray();
				foreach ($exchanges as $key => $value) {
		            $temp['content_type'] = 'text';
		            $temp['title'] = trim($value['name']);
		            $temp['payload'] = trim($value['unique_identifier']);
		            $temp['image_url'] = "https://via.placeholder.com/150";
		            array_push($temparray,$temp);
			    }
		        $jsonData = '{
					    "recipient":{
					        "id":"' . $recipientId . '"
					        },
					        "message":{
							    "text": "Select Your Exchange!",
							    "quick_replies": [
							    	{
							    		"content_type": "text",
							    		"title": "Kraken",
							    		"payload": "kraken",
							    		"image_url": "https://via.placeholder.com/150"
							    	}
							    ]
							  }
					    }';
			}
	        /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    private function marketTextMessage($recipientId, $messageText){
 
    	$user = $this->getUserDetails($recipientId);
		$userdata = json_decode($user);
	    	$exchange_id = 'kraken';
			$exchange_class = '\\ccxt\\' . $exchange_id;
			$exchange = new $exchange_class ();
			$markets = $exchange->load_markets ();
	        $temparray = [];
            $marketsarr = [];
	       
	        foreach ($markets as $key => $value)
	        {
                $basecurrency = $value['quote'];
                $temp['content_type'] = 'text';
	            $temp['title'] = trim($value['quote']);
	            $temp['payload'] = trim($value['quote']);
	            $temp['image_url'] = "https://via.placeholder.com/150";
	           if(!in_array($temp, $temparray)){
	             array_push($marketsarr,$basecurrency);
		       	 array_push($temparray,$temp);
		       }
	        }
	    
    	$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		    /*initialize curl*/
		    $ch = curl_init($url);
	       		       /*prepare response*/
		if( $messageText == 'kraken' )
        { 	
          Cache::put('marketExchangeId', $messageText, 25);
          $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":{
					    "text": "Pick Your Base Currency!",
					    "quick_replies": '. json_encode($temparray).'
					  }
			    }';	    
        } 
        else if ( in_array($messageText, $marketsarr) )
        {    
 
     		Cache::put('marketBaseQuote', $messageText, 25);
            /* prepare response */
            $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":
				        {
				         "text":"Enter Your Market Base Currency !",
				        }
			    }';
        }else if($messageText == 'talk'){
        	//talk to human
        	 /*prepare response*/
			    $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":
				        {
				           "text":"Hey ' . $userdata->first_name . 'Thanks for connecting us. Our Representative connect you soon!",
				        }
			    }';
        }else{
        	 //talk to human
        	 /*prepare response*/
			    $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":
				        {
				           "text":"Hey ' . $userdata->first_name . 'Thanks for connecting us. start converstion simply type hi,
				        }
			    }';
        }
	        /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    private function marketBaseCurrency($recipientId, $messageText){
    	$marketQuoteId = Cache::get('marketBaseQuote');
    	$exchange_id = Cache::get('marketExchangeId');
		$marketBaseCurrency = $this->fetchMarketBaseCurrencyArr($marketQuoteId);
		
		$basecurrencyArr = json_encode( implode(',', $marketBaseCurrency) );
	
		$user = $this->getUserDetails($recipientId);
		$userdata = json_decode($user);
	
    	$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		    /*initialize curl*/
		    $ch = curl_init($url);
			if( in_array(strtoupper($messageText), $marketBaseCurrency) ){
				Cache::put('marketBaseId', $messageText, 25);
				$marketsymbol = strtoupper($messageText).'/'.$marketQuoteId;
				if( in_array($exchange_id, \ccxt\Exchange::$exchanges)){
					
					$exchange_class = '\\ccxt\\' . $exchange_id;
					$exchange = new $exchange_class ();
					$markets = $exchange->fetchTickers();
			        foreach ($markets as $key => $value)
			        {	
		                if( $value['symbol'] == $marketsymbol ){
		                	$marketPrice = $value['last'];        	 
		                }
			        }
			        Cache::put('marketBaselastPrice', $marketPrice, 25);
		        }
		          file_put_contents("php://stderr", "$marketPrice");
       			 $jsonData = '{
				    "recipient":{
				        "id":"' . $recipientId . '"
				        },
				        "message":{
						    "text": "Your Selected Market '.$marketsymbol.' Price is '.$marketPrice.'. To Subscribe This Market Click Below Button YES. !",
						     "quick_replies": [
							    	{
							    		"content_type": "text",
							    		"title": "YES",
							    		"payload": "market_subscribe",
							    		"image_url": "https://via.placeholder.com/150"
							    	},
							    	{
							    		"content_type": "text",
							    		"title": "NO",
							    		"payload": "no_subscribe",
							    		"image_url": "https://via.placeholder.com/150"
							    	}
							    ]
						  }
			    }';
			}else{
				$jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":{
					    "text": "Please Enter vaild Market Base Currency ! eg. like ('.trim($basecurrencyArr,'"').' )",
					  }
			    }';
			}
	        /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    private function selectMarketMessage($recipientId, $messageText){
    	$user = $this->getUserDetails($recipientId);
		$userdata = json_decode($user);
		$marketQuoteId = Cache::get('marketBaseQuote');
    	$exchange_id = Cache::get('marketExchangeId');
    	$marketBaseid = Cache::get('marketBaseId');
    	$lastPrice = Cache::get('marketBaselastPrice');
    	file_put_contents( "php://stderr","$marketQuoteId");
    	file_put_contents( "php://stderr","$exchange_id");
    	file_put_contents( "php://stderr","$marketBaseid");
    	file_put_contents( "php://stderr","$lastPrice");
    	$marketsymbol = strtoupper($marketBaseid).'/'.$marketQuoteId;
		    	file_put_contents( "php://stderr","$marketsymbol");
    	$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		    /*initialize curl*/
		    $ch = curl_init($url);
			
			if( $messageText == 'market_subscribe' ){
				SubscribeMarket::create([
					'user_id' => $recipientId, 
					'exchange_name' => $exchange_id,
					'market_quoteid' => $marketQuoteId, 
					'market_baseid' => strtoupper($marketBaseid),  
					'market_symbol' => $marketsymbol, 
					'market_price'=> $lastPrice
				  ]);
				Cache::pull('marketBaseQuote');
		    	Cache::pull('marketExchangeId');
		    	Cache::pull('marketBaseId');
		    	Cache::pull('marketBaselastPrice');
		        $jsonData = '{
					    "recipient":{
					        "id":"' . $recipientId . '"
					        },
					        "message":{
							    "text": "Thanks for Subscribe Our Market. We will Notify You When be Get SELL/BUY Signal!",
							  }
					    }';
			}else{
			   $jsonData = '{
				    "recipient":{
				        "id":"' . $recipientId . '"
				        },
				        "message":{
						    "text": "Thanks For Connecting Us!",
						}
				    }';
			}
	        /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    private function sendWelcomeMessage($recipientId, $messageText)
    {	
    	 $this->sendAction($recipientId);
    	 Cache::pull('marketBaseQuote');
         // set gretting text array
    	 $grettingtext = array("HI", "HELLO", "HEY", "GET", "START");
    	 //Get user details based on recipient id (nmae,profile,location, etc)
    	 //@param recipientId
		 $user = $this->getUserDetails($recipientId);
		 $userdata = json_decode($user);
    	 $url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
		    /*initialize curl*/
		    $ch = curl_init($url);
	        // check if message text exsit in gretting text
		    // if true the gretting text send 
			$subscribe = SubscribeMarket::where('user_id', $recipientId)->get()->toArray();
			$temparray = [];
			if($subscribe){
		        $temp['type'] = 'postback';
		        $temp['title'] = 'See Your Markets!';
		        $temp['payload'] = 'subscribe_list';
		        array_push($temparray,$temp);
			}
			$temp['type'] = 'postback';
			$temp['title'] = 'Pick Our Exchanges!';
			$temp['payload'] = 'get_exchange';
			array_push($temparray,$temp);	
				        
	       if( in_array(strtoupper($messageText), $grettingtext) ){
		    		/*prepare response*/
			        $jsonData = '{
			        "recipient":{
			        	"id":"' . $recipientId . '"
			        },
			        "message":{
				    "attachment":{
				      "type":"template",
				      "payload":{
				        "template_type":"generic",
				        "elements":[
				           {
				            "title":"Hey ' . $userdata->first_name . ' Good To see You.!",
				            "image_url":"https://lz-bot.herokuapp.com/image/bitcoin-falling-760x400.jpg",
				            "subtitle":"We have the right hat for everyone.",
				            "default_action": {
				              "type": "web_url",
				              "url": "https://lz-bot.herokuapp.com",
				              "webview_height_ratio": "tall",
				            },
				            "buttons": '.json_encode( $temparray ).'
				          }
				        ]
				      }
				    }
				  }
			    }';
	       }else if( strtoupper($messageText) == 'TALKTOHUMAN'){
	       		/*prepare response*/
			    $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":
				        {
				           "text":"Hey ' . $userdata->first_name . 'Thanks for connecting us.!",
				           "quick_replies":[
						      {
						        "content_type":"text",
						        "title":"Start",
						        "payload":"start_default",
						        "image_url":"https://lz-bot.herokuapp.com/image/talktohuman.png"
						      }
						    ]
				        }
			    }';
	       }else{
	       	    /*prepare response*/
			    $jsonData = '{
			    "recipient":{
			        "id":"' . $recipientId . '"
			        },
			        "message":{
			            "text":"Hey ' . $userdata->first_name . ' I dont understand. Try To Enter Correct valaue !",
			        }
			    }';
	       }
		    /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    private function unSubscribeMarketTextMessage($recipientId, $messageText){
    	$user = $this->getUserDetails($recipientId);
		$userdata = json_decode($user);
		$url = 'https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN");
	    /*initialize curl*/
	    $ch = curl_init($url);
    	SubscribeMarket::where([['user_id','=', $recipientId],['market_symbol','=', $messageText]])->delete();
		  		/*prepare response*/
		    $jsonData = '{
		    "recipient":{
		        "id":"' . $recipientId . '"
		        },
		        "message":
			        {
			           "text":"Hey ' . $userdata->first_name . 'Your '.$messageText.' Market Successfully UnSubscribe.! To add new market start a flow",
			           "quick_replies":[
					      {
					        "content_type":"text",
					        "title":"Start",
					        "payload":"start_default",
					        "image_url":"https://lz-bot.herokuapp.com/image/talktohuman.png"
					      }
					    ]
			        }
		    }';
		       /* curl setting to send a json post data */
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		   
		    curl_exec($ch);
            curl_close($ch);
    }
    public function fetchMarketBaseQuote($exchange_id){
    	$exchange_id = 'kraken';
		if( in_array($exchange_id, \ccxt\Exchange::$exchanges)){
			$exchange_class = '\\ccxt\\' . $exchange_id;
			$exchange = new $exchange_class ();
			$markets = $exchange->load_markets ();
            $marketsArr = [];
	      
	        foreach ($markets as $key => $value)
	        {
                $basecurrency = $value['quote'];
	           if(!in_array($basecurrency, $marketsArr)){
	             array_push($marketsArr,$basecurrency);
		       }
	        }
	        return $marketsArr;
		}	
    }
    public function fetchMarketBaseCurrencyArr($marketquote){
 		$exchange_id = 'kraken';
		if( in_array($exchange_id, \ccxt\Exchange::$exchanges)){
			$exchange_class = '\\ccxt\\' . $exchange_id;
			$exchange = new $exchange_class ();
			$markets = $exchange->load_markets ();
            $marketsBaseCurrencyArr = [];
	      
	        foreach ($markets as $key => $value)
	        {	
                if( $value['quote'] == $marketquote){
                	  $basecurrency = $value['base'];
            	   if(!in_array($basecurrency, $marketsBaseCurrencyArr)){
                    array_push($marketsBaseCurrencyArr,$basecurrency);
	               }
                }
	        }
	        return $marketsBaseCurrencyArr;  
        }
    }

}
