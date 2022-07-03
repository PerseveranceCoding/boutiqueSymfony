<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Form\SearchFormType;
use App\Repository\ProduitsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class ProduitsController extends AbstractController
{
    #[Route('/', name: 'app_produits_index', methods: ['GET'])]
    public function index(ProduitsRepository $produitsRepository, Request $request,): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        return $this->render('produits/index.html.twig', [
            'produits' => $produitsRepository->findAll(),
            'produits' => $produitsRepository->findSearch($data),
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit', name: 'app_produits_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitsRepository $produitsRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_produits_index');
        }


        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setUserproduit($this->getUser());
            $produitsRepository->add($produit);
            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produits/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produits/{id<[0-9]+>}', name: 'app_produits_show', methods: ['GET'])]
    /**
     * @ParamConverter("produit", class="App:Produits")
     */
    public function show(Produits $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produits_edit', methods: ['GET', 'POST'])]
    /**
     * @ParamConverter("produit", class="App:Produits")
     */
    public function edit(Request $request, Produits $produit, ProduitsRepository $produitsRepository): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitsRepository->add($produit);
            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produits/{id<[0-9]+>}/delete', name: 'app_produits_delete', methods: ['POST'])]
     /**
     * @ParamConverter("produit", class="App:Produits")
     */
    public function delete(Request $request, Produits $produit, ProduitsRepository $produitsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $produitsRepository->remove($produit);
        }

        return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
    }
}
