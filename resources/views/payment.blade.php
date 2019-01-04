@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
<div class="select-market-plan">
    <p>Select Market Subscription Plan !!</p>
      <div class='form-row'>
        <div class='col-sm-6 form-group required'>
            <select name="market-plan" id="mrktplans" class="form-control">
                <option value="default">Default</option>
                <option value="50">Pay $50 for 50 Market Subscription</option>
                <option value="100">Pay $100 for 100 Market Subscription</option>
            </select>
        </div>
    </div>
</div>

@if(Session::has('error'))
<div class="alert alert-danger">
  {{ Session::get('error')}}
</div>
@endif


@if(Session::has('success'))
<div class="alert alert-success">
  {{ Session::get('success')}}
</div>
@endif

<div class="payment-method">
<p>Select Your Payment Method!!!</p>
<button class="strip-btn button"><span>Pay Using Stripe </span></button>
<button class="paypal-btn button"><span>Pay Using PayPal </span></button>
</div>

<form class="w3-container w3-display-middle w3-card-4 " method="POST" id="paypal-payment"  action="{{ URL::route('paywithpaypal') }}">
  {{ csrf_field() }}

    <div class='form-row'>
        <div class='col-sm-6 form-group required'>
            <label class="control-label w3-text-blue"><b>Enter Amount</b></label>
            <input class="form-control w3-input w3-border" name="amount" id="paypal-amount" type="text">
        </div>
    </div>
    <div class='form-row'>
        <div class='col-sm-6 form-group required'>
            <label class="control-label w3-text-blue"><b>Your Plan Description</b></label>
            <input class="form-control w3-input w3-border" name="market_plan" id="market-plan"  type="text" readonly="readonly">
        </div>
    </div>
    <div class='form-row'>
        <div class='col-sm-6 form-group required'>
         <button class='w3-btn w3-blue form-control btn btn-primary submit-button'
                type='submit' style="margin-top: 10px;">Pay with PayPal</button>
        </div>
  </div>
</form>

    <form accept-charset="UTF-8" action="{{ URL::route('payStripe') }}" class="require-validation"
    data-cc-on-file="false"
    data-stripe-publishable-key="pk_test_CMauDzV8ggipwV2j6A6d4PBh"
    id="stripe-payment" method="post">
    {{ csrf_field() }}
    <div class='form-row'>
        <div class='col-sm-6 form-group required'>
            <label class='control-label'>Name on Card</label> <input
                class='form-control'  type='text'>
        </div>
    </div>
    <div class='form-row'>
        <div class='col-sm-6 form-group  required'>
            <label class='control-label'>Card Number</label> <input
                autocomplete='off' class='form-control card-number' size='20'
                type='text'>
        </div>
    </div>
    <div class='form-row'>
        <div class='col-xs-4 form-group  required'>
            <label class='control-label'>CVC</label> <input autocomplete='off'
                class='form-control card-cvc' placeholder='ex. 311' size='4'
                type='text'>
        </div>
        <div class='col-xs-4 form-group  required'>
            <label class='control-label'>Expiration</label> <input
                class='form-control card-expiry-month' placeholder='MM' size='2'
                type='text'>
        </div>
        <div class='col-xs-4 form-group expiration required'>
            <label class='control-label'> </label> <input
                class='form-control card-expiry-year' placeholder='YYYY' size='4'
                type='text'>
        </div>
    </div>
    <div class='form-row'>
        <div class='col-md-12'>
            <div class='form-control total btn btn-info'>
                Total: <span class='amount'>$200</span>
            </div>
        </div>
    </div>
    <div class='form-row'>
        <div class='col-md-12 form-group'>
            <button class='form-control btn btn-primary submit-button'
                type='submit' style="margin-top: 10px;">Pay »</button>
        </div>
    </div>

</form>

                </div>
            </div>
        </div>
    </div>
</div>

<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>

<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<Script>
$(function() {

setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 30000); // <-- time in milliseconds


    $("#mrktplans").on('change', function(){
     
        if($(this).val() == 'default'){
            $(".payment-method").hide();
        }else{
              $(".payment-method").show();
              $(".amount").text('$' +$(this).val());
              $("#paypal-amount").val($(this).val());
              $("#market-plan").val($(this).find('option:selected').text());
       
        }
    })

var $stripfrom = $("#stripe-payment");
var $paypalform = $("#paypal-payment");

$(".strip-btn").on('click', function(){
    $stripfrom.toggle();
    $(".paypal-btn").toggle();
})


$(".paypal-btn").on('click', function(){
    $paypalform.toggle();
    $(".strip-btn").toggle();
})

$stripfrom.on('submit', function(e) {

    if (!$stripfrom.data('cc-on-file')) {
      e.preventDefault();
      Stripe.setPublishableKey($stripfrom.data('stripe-publishable-key'));
      Stripe.createToken({
        number: $('.card-number').val(),
        cvc: $('.card-cvc').val(),
        exp_month: $('.card-expiry-month').val(),
        exp_year: $('.card-expiry-year').val()
      }, stripeResponseHandler);
    }
  });

function stripeResponseHandler(status, response) {
    if (response.error) {
        $('.error')
            .removeClass('hide')
            .find('.alert')
            .text(response.error.message);
    } else {

        console.log(response);
        // token contains id, last4, and card type
        var token = response['id'];
        // insert the token into the form so it gets submitted to the server
        $stripfrom.find('input[type=text]').empty();
        $stripfrom.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
        $stripfrom.get(0).submit();
    }
}

});

</script>
@endsection
