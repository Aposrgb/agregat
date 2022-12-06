<?php

namespace App\Controller\Admin;

use App\Helper\Exception\ApiException;
use App\Service\ImportService;
use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload')]
class UploadController extends AbstractController
{
    #[Route('/product/csv', name: 'upload_product_csv', methods: ['POST'])]
    public function uploadProduct(
        Request          $request,
        ValidatorService $validatorService,
        ImportService    $importService,
    ): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        if (!$file) {
            throw new ApiException('Файл не загружен');
        }
        if ($file->getClientOriginalExtension() != 'csv') {
            throw new ApiException('Неверный тип расширения файла');
        }
        $validatorService->validateImagesExtension([$file], ['text/csv', 'text/plain']);
        $importService->importProductsFromCSV($file);
        return $this->json([]);
    }
}