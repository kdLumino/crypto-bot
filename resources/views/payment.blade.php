@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Select Your Payment Method!!!
  <form action="{{ URL::route('payStripe') }}" method="POST">
            {{ csrf_field() }}
        <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="{{ config('services.stripe.key') }}"
            data-amount="1000"
            data-name="Demo Book"
            data-description="This is good start up book."
            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
            data-locale="auto">
        </script>
        </form>
                    <a href="{{ URL::route('payStripe') }}" class="btn btn-default"> Stripe </a>

<a href="{{ URL::route('payStripe') }}" class="btn btn-default">PyPal </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
