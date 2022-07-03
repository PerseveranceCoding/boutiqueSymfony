<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Produits;

class StripeService
{
    private $privateKey;

    public function __construct()
    {
        if($_ENV['APP_ENV']  === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    /**
     * @param Produits $produit
     * @return \Stripe\PaymentIntent
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function paymentIntent(Produits $produit)
    {
        \Stripe\Stripe::setApiKey($this->privateKey);

        return \Stripe\PaymentIntent::create([
            'amount' => $produit->getPrix() * 100,
            'currency' => Order::DEVISE,
            'payment_method_types' => ['card']
        ]);
    }

    public function paiement(
        $amount,
        $currency,
        $description,
        array $stripeParameter
    )
    {
        \Stripe\Stripe::setApiKey($this->privateKey);
        $payment_intent = null;

        if(isset($stripeParameter['stripeIntentId'])) {
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }

        if($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            //TODO
        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

    /**
     * @param array $stripeParameter
     * @param Produits $produit
     * @return \Stripe\PaymentIntent|null
     */
    public function stripe(array $stripeParameter, Produits $produit)
    {
        return $this->paiement(
            $produit->getPrix() * 100,
            Order::DEVISE,
            $produit->getTitre(),
            $stripeParameter
        );
    }
}