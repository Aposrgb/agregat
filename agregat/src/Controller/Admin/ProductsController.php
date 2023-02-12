<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use App\Service\FileUploadService;
use App\Service\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #[Route('/upload', name: 'app_products_upload', methods: ['GET'])]
    public function uploadProducts(): Response
    {
        return $this->render('upload/upload_products.html.twig');
    }

    #[Route('/', name: 'app_products_index', methods: ['GET'])]
    public function index(Request $request, ProductsRepository $productsRepository): Response
    {
        $search = $request->query->get('search');
        if ($search && strlen($search) > 0) {
            $products = $productsRepository->findByTitle($search);
        } else {
            $products = $productsRepository->findAll();
        }
        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductsRepository $productsRepository, FileUploadService $fileUploadService): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $request->files->get('products')['img'] ?? null;
            if ($img) {
                if ($product->getImg()) {
                    $fileUploadService->deleteFile($product->getImg());
                }
                $product->setImg(
                    $fileUploadService->upload($img, ProductsService::PRODUCTS_FILE_PATH)
                );
            }
            $productsRepository->save($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, ProductsRepository $productsRepository, FileUploadService $fileUploadService): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $request->files->get('products')['img'] ?? null;
            if ($img) {
                if ($product->getImg()) {
                    $fileUploadService->deleteFile($product->getImg());
                }
                $product->setImg(
                    $fileUploadService->upload($img, ProductsService::PRODUCTS_FILE_PATH)
                );
            }
            $productsRepository->save($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productsRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }
}
