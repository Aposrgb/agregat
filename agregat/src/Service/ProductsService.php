<?php

namespace App\Service;

use App\Entity\Categories;
use App\Entity\Products;
use App\Helper\Exception\ApiException;
use App\Helper\Filter\ProductsFilter;
use App\Helper\Mapped\ProductFilter;
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

    /**
     * @param Products[] $products
     */
    public function getFilterByProducts(array $products, ProductsFilter $productsFilter): ProductFilter
    {
        $productFilter = new ProductFilter();
        $products = array_filter($products, function (Products $product) use ($productsFilter) {
            $isFilter = true;
            if ($productsFilter->getIsActual()) {
                $isFilter = $product->isIsActual() == $productsFilter->getIsActual();
            }
            if($productsFilter->getCategory() && $product->getCategories()){
                $isFilter = $product->getCategories()->getId() == $productsFilter->getCategory();
            }
            return $isFilter;
        });
        foreach ($products as $product) {
            if ($category = $product->getCategories()) {
                $productFilter->addCategory($category);
            }
            if ($subCategories = $product->getSubCategories()) {
                $productFilter->addSubCategory($subCategories);
            }
            if ($price = $product->getPrice()) {
                $productFilter->setMinPrice(min($price, $productFilter->getMinPrice()));
                $productFilter->setMaxPrice(max($price, $productFilter->getMaxPrice()));
            }
            if ($rating = $product->getAverageRating()) {
                $productFilter->setMinRating(min($rating, $productFilter->getMinRating()));
                $productFilter->setMaxRating(max($rating, $productFilter->getMaxRating()));
            }
        }
        return $productFilter;
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