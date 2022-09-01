<?php

namespace App\Http\Controllers\Webhooks;

use App\Events\WalletEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeHook extends Controller
{

    public function handle()
    {
        Log::debug("Getting stipe event");

        $endpoint_secret = env("STRIPE_WEBHOOK_SEC");

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::debug($e->getMessage());
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::debug($e->getMessage());
            http_response_code(400);
            exit();
        }

        Log::debug($event->type);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                break;

            default:

                break;
        }
    }


    private function handlePaymentSuccess($data)
    {

        $user = User::where('stripe_id', $data->customer)->first();

        Log::debug(print_r($data, true));
        Log::debug(print_r($user->toArray(), true));


        if (!$user) {
            Log::debug("No user found " . $data->customer);
            return false;
        }

        try {
            DB::beginTransaction();
            $payment = new Payment();

            $payment->user_id = $user->id;
            $payment->payment_id = $data->id;
            $payment->uuid =
                $payment->amount = $data->amount;

            if ($data->status == 'succeeded') {
                $payment->is_success = true;
            } else {
                Log::debug("Payment faild for " . $user->id);
                Log::debug(print_r($data, true));

                $payment->is_success = false;
            }

            $payment->save();

            if ($payment->is_success == true) {

                Transaction::add($payment->amount, $user, 'top-up', 'Added into wallet');

                //0.3 $ charge for the transaction
                Transaction::add(50, $user, 'paid', 'One transaction fee');

                $user->balance = ($user->balance + $payment->amount) - 50;
                $user->save();
            }
            DB::commit();

            event(new WalletEvent($user, ["status" => 'success', "balance" => $user->balance]));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Faild to store data " . $e->getMessage() . " | " . $e->getLine());
            Log::debug($e->getTraceAsString());
            Log::debug(print_r($data, true));
        }
    }
}
