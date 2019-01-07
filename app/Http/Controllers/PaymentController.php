<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Illuminate\Support\Facades\Input;
use App\Payments;
use Auth;
use Cache;
use Config;

/** Use Stripe Payment Class **/
use Stripe\Stripe;
use Stripe\Charge;

/** All Paypal Details class **/
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;


class PaymentController extends Controller
{
      
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);

    }

    public function index(){

         return view('payment');
    }

    public function stripe(Request $request)
    {
       
       \Stripe\Stripe::setApiKey ( 'sk_test_0lB8nky2y0n5FxOY27C5b9lx' );
        try {
            \Stripe\Charge::create ( array (
                    "amount" => 300 * 100,
                    "currency" => "usd",
                    "source" => $request->input ( 'stripeToken' ), // obtained with Stripe.js
                    "description" => "Test payment." 
            ) );
            Session::flash ( 'success-message', 'Payment done successfully !' );
           return view('payment');
        } catch ( \Exception $e ) {
            Session::flash ( 'fail-message', "Error! Please Try again." );
            return view('payment');
        }
    }

   public function payWithpaypal(Request $request)
    {   
        // dd($request->get('items'));
        $payer = new Payer();
                $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName($request->get('items') . 'markets') /** item name **/
                    ->setCurrency('USD')
                    ->setQuantity(1)
                    ->setPrice($request->get('amount_hidden')); /** unit price **/
        $item_list = new ItemList();
                $item_list->setItems(array($item_1));
        $amount = new Amount();
                $amount->setCurrency('USD')
                    ->setTotal($request->get('amount_hidden'));
        $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription($request->get('market_plan'));
        $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(URL::route('status')) /** Specify return URL **/
                    ->setCancelUrl(URL::route('status'));
        $payment = new Payment();
                $payment->setIntent('Sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirect_urls)
                    ->setTransactions(array($transaction));
                /** dd($payment->create($this->_api_context));exit; **/
                try {
        $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
        if (\Config::get('app.debug')) {
        Session::flash('error', 'Connection timeout');
                        return Redirect::route('paywithpaypal');
        } else {
        Session::flash('error', 'Some error occur, sorry for inconvenient');
                        return Redirect::route('paywithpaypal');
        }
        }
        foreach ($payment->getLinks() as $link) {
        if ($link->getRel() == 'approval_url') {
        $redirect_url = $link->getHref();
                        break;
        }
        }


        /** add payment ID to session **/
        Session::flash('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
        /** redirect to paypal **/
        return Redirect::away($redirect_url);
        }
        Session::flash('error', 'Unknown error occurred');
                return Redirect::route('paywithpaypal');
    }

    public function getPaymentStatus()
    {

        /** Get the payment ID before session clear **/
                $payment_id = Session::get('paypal_payment_id');
        /** clear the session payment ID **/
                Session::forget('paypal_payment_id');
                if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
        Session::flash('error', 'Payment failed');
                    return Redirect::route('payment');
        }

     
        $payment = Payment::get($payment_id, $this->_api_context);
                $execution = new PaymentExecution();
                $execution->setPayerId(Input::get('PayerID'));

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
      
        if ($result->getState() == 'approved') {
         
        $transaction_id = $result->transactions[0]->related_resources[0]->sale->id;
               
        Payments::create([
                'auth_user_id' => Auth::user()->id, 
                'fb_user_id' => $fb_id,
                'transaction_id' => $transaction_id, 
                'payment_id' => $result->id, 
                'transaction_price' => $result->transactions[0]->amount->total,  
                'plan_name' => $result->transactions[0]->item_list->items[0]->name, 
                'payment_method'=> 'paypal'
        ]);
                                
        Session::flash('success', 'Payment success');
                    return Redirect::route('payment');
        }
        Session::flash('error', 'Payment failed');
                return Redirect::route('payment');
    }


}
