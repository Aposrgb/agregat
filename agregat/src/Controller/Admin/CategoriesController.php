<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\SubCategories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use App\Service\CategoryService;
use App\Service\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'app_categories_index', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('categories/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoriesRepository $categoriesRepository, FileUploadService $fileUploadService): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $request->files->get('categories')['img'] ?? null;
            if ($img) {
                $category->setImg(
                    $fileUploadService->upload($img, CategoryService::PATH_TO_IMAGE)
                );
            } else {
                return $this->renderForm('categories/new.html.twig', [
                    'category' => $category,
                    'form' => $form,
                ]);
            }
            $categoriesRepository->save($category, true);

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, CategoriesRepository $categoriesRepository, FileUploadService $fileUploadService): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $img = $request->files->get('categories')['img'] ?? null;
            if ($img) {
                $fileUploadService->deleteFile($category->getImg());
                $category->setImg(
                    $fileUploadService->upload($img, CategoryService::PATH_TO_IMAGE)
                );
            }
            $categoriesRepository->save($category, true);

            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, CategoriesRepository $categoriesRepository, FileUploadService $fileUploadService): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $category->getSubCategories()->forAll(function (int $index, SubCategories $item) {
                $item->setCategory(null);
            });
            foreach ($category->getProducts() as $product){
                $product->setCategories(null);
            }
            if ($category->getImg()) {
                $fileUploadService->deleteFile($category->getImg());
            }
            $categoriesRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
    }
}
