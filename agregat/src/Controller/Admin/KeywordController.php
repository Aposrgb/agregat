<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/keyword')]
class KeywordController extends AbstractController
{
    #[Route('/', name: 'app_keyword_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('keyword/index.html.twig', [
            'products' => $productsRepository->findByKeyword(),
        ]);
    }

    #[Route('/{id}', name: 'app_keyword_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('keyword/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_keyword_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsRepository->save($product, true);

            return $this->redirectToRoute('app_keyword_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('keyword/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
