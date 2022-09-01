<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripeService extends Controller
{
    private $stripe;

    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRATE_KEY'));
        $this->stripe = new StripeClient(env('STRIPE_SECRATE_KEY'));
    }

    public function customer($email)
    {
        try {
            $customer = Customer::create([
                'email' => $email,
            ]);

            return $customer;
        } catch (Exception $e) {
            Log::debug("Error while adding customer " . $e->getMessage());

            return false;
        }
    }

    public function getSercrate($amount)
    {
        $user = Auth::user();
        $customerId = $user->stripe_id;

        if (empty($customerId)) {
            $cust =  $this->customer($user->email);

            if ($cust) {
                $user->stripe_id = $cust->id;
                $user->save();
                $customerId = $cust->id;
            }
            $customerId = $cust->id;
        }

        return PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => 'usd',
            'customer' => $customerId
        ]);
    }

    public function addPaymentMethod($pm)
    {
        $customerId = Auth::user()->stripe_id;

        $this->stripe->paymentMethods->attach(
            $pm,
            ['customer' => $customerId]
        );
    }

    public function addBankAccount($token)
    {
        $user = Auth::user();
        $customerId = Auth::user()->stripe_id;

        if (empty($customerId)) {
            $cust =  $this->customer($user->email);

            if ($cust) {
                $user->stripe_id = $cust->id;
                $user->save();
                $customerId = $cust->id;
            }

        }

        $account =  $this->stripe->customers->createSource(
            $customerId,
            ['source' => $token]
          );
        
        return $account;
    }

    public function getBankAccounts()
    {
        $user = Auth::user();
        $customerId = Auth::user()->stripe_id;

        $accounts =  $this->stripe->customers->allSources(
            $customerId,
            ['object' => 'bank_account']
          );

        return $accounts;
    }

    public function deleteBankAccount($bank_id)
    {
        $user = Auth::user();
        $customerId = Auth::user()->stripe_id;

        $account =  $this->stripe->customers->deleteSource(
            $customerId,
            $bank_id,
            []
          );

        return $account;
    }

}
