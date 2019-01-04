<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class Payment extends Controller
{   
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

         return view('payment');
    }

    public function stripe(Request $request)
    {
      Stripe::setApiKey(config('services.stripe.secret'));
 
        $token = request('stripeToken');
 
        $charge = Charge::create([
            'amount' => 1000,
            'currency' => 'usd',
            'description' => 'Test Book',
            'source' => $token,
        ]);
 
        return 'Payment Success!';
    }
}
