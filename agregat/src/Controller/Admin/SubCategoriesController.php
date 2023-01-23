<?php

namespace App\Controller\Admin;

use App\Entity\SubCategories;
use App\Form\SubCategoriesType;
use App\Repository\CategoriesRepository;
use App\Repository\SubCategoriesRepository;
use App\Service\FileUploadService;
use App\Service\SubCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sub/categories')]
class SubCategoriesController extends AbstractController
{
    #[Route('/', name: 'app_sub_categories_index', methods: ['GET'])]
    public function index(SubCategoriesRepository $subCategoriesRepository): Response
    {
        return $this->render('sub_categories/index.html.twig', [
            'sub_categories' => $subCategoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sub_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SubCategoriesRepository $subCategoriesRepository, FileUploadService $fileUploadService, CategoriesRepository $categoriesRepository): Response
    {
        $subCategory = new SubCategories();
        $form = $this->createForm(SubCategoriesType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryId = $form->get('categories')->getData();
            if ($categoryId) {
                $category = $categoriesRepository->find($categoryId);
                if ($category) {
                    $subCategory->setCategory($category);
                } else {
                    return $this->renderForm('sub_categories/new.html.twig', [
                        'sub_category' => $subCategory,
                        'form' => $form,
                    ]);
                }
            } else {
                return $this->renderForm('sub_categories/new.html.twig', [
                    'sub_category' => $subCategory,
                    'form' => $form,
                ]);
            }
            $img = $request->files->get('sub_categories')['img'] ?? null;
            if ($img) {
                if ($subCategory->getImg()) {
                    $fileUploadService->deleteFile($subCategory->getImg());
                }
                $subCategory->setImg(
                    $fileUploadService->upload($img, SubCategoryService::PATH_TO_IMAGE)
                );
            }
            $subCategoriesRepository->save($subCategory, true);

            return $this->redirectToRoute('app_sub_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sub_categories/new.html.twig', [
            'sub_category' => $subCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_categories_show', methods: ['GET'])]
    public function show(SubCategories $subCategory): Response
    {
        return $this->render('sub_categories/show.html.twig', [
            'sub_category' => $subCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sub_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubCategories $subCategory, SubCategoriesRepository $subCategoriesRepository, FileUploadService $fileUploadService, CategoriesRepository $categoriesRepository): Response
    {
        $form = $this->createForm(SubCategoriesType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryId = $form->get('categories')->getData();
            if ($categoryId) {
                $category = $categoriesRepository->find($categoryId);
                if ($category) {
                    $subCategory->setCategory($category);
                }
            }
            $img = $request->files->get('sub_categories')['img'] ?? null;
            if ($img) {
                if ($subCategory->getImg()) {
                    $fileUploadService->deleteFile($subCategory->getImg());
                }
                $subCategory->setImg(
                    $fileUploadService->upload($img, SubCategoryService::PATH_TO_IMAGE)
                );
            }
            $subCategoriesRepository->save($subCategory, true);

            return $this->redirectToRoute('app_sub_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sub_categories/edit.html.twig', [
            'sub_category' => $subCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sub_categories_delete', methods: ['POST'])]
    public function delete(Request $request, SubCategories $subCategory, SubCategoriesRepository $subCategoriesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $subCategory->getId(), $request->request->get('_token'))) {
            $subCategoriesRepository->remove($subCategory, true);
        }

        return $this->redirectToRoute('app_sub_categories_index', [], Response::HTTP_SEE_OTHER);
    }
}
