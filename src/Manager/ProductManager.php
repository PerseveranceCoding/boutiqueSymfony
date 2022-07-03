<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Produits;
use App\Entity\Users;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var StripeService
     */
    protected $stripeService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StripeService $stripeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StripeService $stripeService
    ) {
        $this->em = $entityManager;
        $this->stripeService = $stripeService;
    }

    public function getProduits()
    {
        return $this->em->getRepository(Produits::class)
            ->findAll();
    }

    /**
     * @param \App\Entity\Users $user
     * @return mixed
     */
    public function countSoldeOrder(Users $user)
    {
        return $this->em->getRepository(Order::class)
            ->countSoldeOrder($user);
    }
    
    public function getOrders(Users $user)
    {
        return $this->em->getRepository(Order::class)
            ->findByUser($user);
    }

    public function intentSecret(Produits $produit)
    {
        $intent = $this->stripeService->paymentIntent($produit);

        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameter
     * @param Produits $produit
     * @return array|null
     */
    public function stripe(array $stripeParameter, Produits $produit)
    {
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $produit);

        if($data) {
            $resource = [
                'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripeId' => $data['charges']['data'][0]['id'],
                'stripeStatus' => $data['charges']['data'][0]['status'],
                'stripeToken' => $data['client_secret']
            ];
        }

        return $resource;
    }

    /**
     * @param array $resource
     * @param Produits $produit
     * @param Users $user
     */
    public function create_subscription(array $resource, Produits $produit, Users $user)
    {
        $order = new Order();
        $order->setUser($user);
        $order->setProduit($produit);
        $order->setPrix($produit->getPrix());
        $order->setReference(uniqid('', false));
        $order->setBrandStripe($resource['stripeBrand']);
        $order->setLast4Stripe($resource['stripeLast4']);
        $order->setIdChargeStripe($resource['stripeId']);
        $order->setStripeToken($resource['stripeToken']);
        $order->setStatusStripe($resource['stripeStatus']);
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($order);
        $this->em->flush();
    }
}
