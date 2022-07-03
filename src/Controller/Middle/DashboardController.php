<?php

namespace App\Controller\Middle;

use App\Entity\Produits;
use App\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DashboardController extends AbstractController
{
    /**
     * @Route("/user/payment/{id}/show", name="payment", methods={"GET", "POST"})
     * @ParamConverter("produit", class="App:Produits")
     * @param Produits $produit
     * @return Response
     */
    public function payment(Produits $produit, ProductManager $productManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/payment.html.twig', [
            'user' => $this->getUser(),
            'intentSecret' => $productManager->intentSecret($produit),
            'produit' => $produit
        ]);
    }

    /**
     * @Route("/user/subscription/{id}/paiement/load", name="subscription_paiement", methods={"GET", "POST"})
     * @ParamConverter("produit", class="App:Produits")
     * @param Produits $produit
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function subscription(
        Produits $produit,
        Request $request,
        ProductManager $productManager
    ){
        $user = $this->getUser();

        if($request->getMethod() === "POST") {
            $resource = $productManager->stripe($_POST, $produit);

            if(null !== $resource) {
                $productManager->create_subscription($resource, $produit, $user);

                return $this->render('user/reponse.html.twig', [
                    'produit' => $produit
                ]);
            }
        }

        return $this->redirectToRoute('payment', ['id' => $produit->getId()]);
    }

    /**
     * @Route("/user/payment/orders", name="payment_orders", methods={"GET"})
     * 
     * @param ProductManager $productManager
     * @return Response
     */
    public function payment_orders(ProductManager $productManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/payment_story.html.twig', [
            'user' => $this->getUser(),
            'orders' => $productManager->getOrders($this->getUser()),
            'sumOrder' => $productManager->countSoldeOrder($this->getUser()),
        ]);
    }
}