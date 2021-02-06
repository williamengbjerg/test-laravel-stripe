<?php

namespace App\Http\Controllers;

use App\Http\Requests\subscribeToStripePlan;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Stripe\StripeClient;

/**
 * Class StripeSubscriptionController
 *
 * @package App\Http\Controllers
 */
class StripeSubscriptionController extends Controller
{

    /**
     * Output all plans created on Stripe account
     *
     * @return Collection
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function listPlansFromStripe(): Collection
    {
        $stripe = new StripeClient(config('app.STRIPE_SECRET'));

        return collect($stripe->plans->all()->data)
                ->each(function ($plan) use ($stripe) {
                        try {
                            $product = $stripe->products->retrieve($plan->product, []);
                        } catch (\Exception) {
                            throw new \RuntimeException("Product does not exist");
                        }
                        $plan->product = $product;
                    }
                );
    }

    /**
     * Output HTML
     *
     * @return Factory|View|Application
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function showPlanHTML(): Factory|View|Application
    {
        $plans  = $this->listPlansFromStripe();
        $user   = Auth::user();

        return view('dashboard', [
            'user'    => $user,
            'intent'  => $user->createSetupIntent(), // Create a new SetupIntent instance.
            'plans'   => $plans
        ]);
    }

    /**
     * Subscribe to a new StripePlan
     *
     * @param \App\Http\Requests\subscribeToStripePlan $subscribeToStripePlan
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribeToPlan(subscribeToStripePlan $subscribeToStripePlan): RedirectResponse
    {
        $user = Auth::user();

        $paymentMethod  = $subscribeToStripePlan->input('payment_method');
        $planName       = $subscribeToStripePlan->input('plan_name');
        $plan           = $subscribeToStripePlan->input('pricing_plan');

        $user->createOrGetStripeCustomer();
        $user->addPaymentMethod($paymentMethod);

        try {
            $user->newSubscription($planName, $plan)->create($paymentMethod, [
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }

        return redirect(route('home'));

    }

}
