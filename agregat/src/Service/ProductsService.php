<?php

namespace App\Service;

use App\Entity\Products;
use App\Helper\Exception\ApiException;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ProductsService
{
    const PRODUCTS_FILE_PATH = "/upload/products/img/";

    const AVAILABLE_IMAGE_EXTENSIONS = [
        'image/jpg',
        'image/jpeg',
        'image/png',
    ];

    public function __construct(
        protected ProductsRepository $productsRepository,
        protected FileUploadService  $fileUploadService,
    )
    {
    }

    public function getProductById(string|int $id): Products
    {
        $products = $this->productsRepository->find($id);
        if (!$products) {
            throw new ApiException(message: 'Не найден продукт', status: Response::HTTP_NOT_FOUND);
        }
        return $products;
    }

    public function uploadImg(Products $products, UploadedFile $file): Products
    {
        if ($image = $products->getImg()) {
            if (!$this->fileUploadService->deleteFile($image)) {
                throw new ApiException(message: 'Не удалось добавить баннер');
            }
        }
        return $products->setImg(
            $this->fileUploadService->upload($file, self::PRODUCTS_FILE_PATH)
        );
    }


}