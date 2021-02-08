<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <small class="text-gray-700">DEMO PAGE - <strong>ONLY FOR TEST</strong></small>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- should probably seperate pages--}}
                    @if ($user->subscriptions()->first() !== null)
                        You are subscribed. Fancy stats etc. here?
                        <br>
                        <hr class="my-3">
                        <div class="mt-3 float-right text-sm">
                            (Saved payment method) **** **** **** {{ $user->card_last_four }}
                        </div>

                        <br>
                        <br>

                    @else
                        <form action="{{ route('postSubscribeToPlan') }}" method="POST" id="subscribe-form">
                            @csrf

                            <div class="form-group">
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                Hi {{ $user->name }}, <br>
                                                You are not subscribed to any plans. Subscribe to a plan below and get full access.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <ul class="relative bg-white rounded-md -space-y-px">
                                @forelse($plans as $key => $plan)
                                    <li >
                                        <div class="border-red-500">
                                            <div onclick="planName('{{ $plan->id }}');" class="border-gray-200 relative border @if($loop->first) rounded-tl-md rounded-tr-md @endif @if($loop->last) rounded-bl-md rounded-br-md @endif p-4 flex flex-col md:pl-4 md:pr-6 md:grid md:grid-cols-3">
                                                <label class="flex items-center text-sm cursor-pointer">
                                                    <input name="pricing_plan" id="pricing_plan-{{ $plan->id }}" type="radio" value="{{ $plan->id }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 cursor-pointer border-gray-300">
                                                    <span class="ml-3 font-medium text-gray-900 font-bold">
                                                        {{ $plan->product->name }}
                                                    </span>
                                                </label>
                                                <input type="hidden" name="plan_name-{{ $plan->id }}" id="plan_name-{{ $plan->id }}" value="{{ Str::slug($plan->product->name) }}">
                                                <p id="plan-option-pricing-{{ $key }}" class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-center">
                                                    <span class="font-medium text-gray-900">
                                                        {{ \App\helpers::planAmount($plan->amount) }} {{ strtoupper($plan->currency) }}
                                                    </span>
                                                    <span class="text-gray-500">/ {{ $plan->interval }}</span>
                                                </p>
                                                <p id="plan-option-limit-{{ $key }}" class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-right">
                                                    {{ $plan->product->description }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    There's no current plan to choose
                                @endforelse
                            </ul>

                            <div class="rounded bg-gray-50 mt-4 p-5">
                                <label for="card-element" class="text-sm">
                                    <strong>Enter Credit or debit card</strong>
                                </label>
                                <div id="card-element" class="border rounded p-3 mt-2">
                                </div>
                                <!-- Used to display form errors. -->
                                <div id="card-errors" role="alert"></div>
                            </div>

                            <input id="setPlanName" name="planName" type="hidden" value="monthly">
                            <input id="card-holder-name" type="hidden" value="{{ $user->name }}">

                            <div class="stripe-errors"></div>
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-4">
                                <button type="submit" id="card-button" data-secret="{{ $intent->client_secret }}" class="inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-blue-600">
                                    Subscribe
                                </button>
                            </div>

                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @if ($user->subscriptions()->first() === null)

        <script>
            function planName(id) {
                const radioCheckbox = document.getElementById("pricing_plan-" + id);
                const getPlanName   = document.getElementById("plan_name-" + id);
                const setPlanName   = document.getElementById("setPlanName");

                if (radioCheckbox.checked === true){
                    setPlanName.value = getPlanName.value;
                }
            }
        </script>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            let stripe = Stripe('{{ config('app.STRIPE_KEY') }}');
            var elements = stripe.elements();
            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            var card = elements.create('card', {
                hidePostalCode: true,
                style: style
            });
            card.mount('#card-element');
            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            const cardHolderName = document.getElementById('card-holder-name');
            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;
            cardButton.addEventListener('click', async (e) => {
                console.log("attempting subscription");
                const { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: { name: cardHolderName.value }
                        }
                    }
                );
                if (error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                } else {
                    paymentMethodHandler(setupIntent.payment_method);
                }
            });
            function paymentMethodHandler(payment_method) {
                var form = document.getElementById('subscribe-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method');
                hiddenInput.setAttribute('value', payment_method);
                form.appendChild(hiddenInput);
                form.submit();
            }
        </script>
    @endif

</x-app-layout>
