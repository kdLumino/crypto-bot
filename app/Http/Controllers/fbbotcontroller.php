<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use view;

class fbbotcontroller extends Controller
{

    public function callback(Request $request){
       $data = request()->all();
              //get the user’s id
        $id = $data["entry"][0]["messaging"][0]["sender"]["id"];
        $senderMessage = $data["entry"][0]["messaging"][0]["message"];
     
        if(!$senderMessage){
            $this->sendTextMessage($id, "Hello");
        }
        
    }
    private function sendTextMessage($recipientId, $messageText)
    {

    	file_put_contents( "php://stderr","$messageText");
        $messageData = [
            "recipient" => [
                "id" => $recipientId
            ],
            "message" => [
                "text" => $messageText
            ]
        ];
 
        $ch = curl_init('https://graph.facebook.com/v3.2/me/messages?access_token=' . env("PAGE_ACCESS_TOKEN"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
        curl_exec($ch);
    }

}
