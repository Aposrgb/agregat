<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Products;
use App\Repository\BrandRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportService
{
    public function __construct(
        protected ParameterBagInterface  $parameterBag,
        protected BrandRepository        $brandRepository,
        protected EntityManagerInterface $entityManager,
        protected ProductsRepository     $productsRepository
    )
    {
    }

    public function importProductsFromCSV(UploadedFile $data): void
    {
        $data = $data->getContent();
        $data = str_getcsv($data, "\n");
        $brands = $this->brandRepository->findAll();
        $products = $this->productsRepository->findAll();
        $brandNames = array_map(fn(Brand $brand) => $brand->getName(), $brands);
        $productNames = array_map(fn(Products $product) => $product->getTitle(), $products);
        $productIds = array_map(fn(Products $product) => $product->getId(), $products);
        $foundedProducts = [];
        $i = 12;
        for (; $i < count($data); $i++) {
            $csv = str_getcsv($data[$i], ';');
            $name = trim($csv[0]);
            if (($indexName = array_search($name, $productNames)) !== false) {
                $product = $products[$indexName];
                $foundedProducts[] = $productIds[$indexName];
            } else {
                $product = new Products();
                $this->entityManager->persist($product);
            }
            $product
                ->setTitle($name)
                ->setArticle($csv[3])
                ->setBalanceStock((int)$csv[1])
                ->setDiscountPrice($this->parsePriceFloat($csv[7]))
                ->setPrice($this->parsePriceFloat($csv[5]))
                ->setCode1C($csv[2]);
            if ($csv[4] != '' && !empty($csv[4])) {
                if ($index = array_search($csv[4], $brandNames)) {
                    $product->setBrand($brands[$index]);
                } else {
                    $product->setBrand((new Brand())->setName($csv[4]));
                }
            }
        }
        $resIds = array_diff($productIds, $foundedProducts);
        $removedProducts = $this->productsRepository->findBy(['id' => $resIds]);
        foreach ($removedProducts as $product){
            $this->entityManager->remove($product);
        }
        $this->entityManager->flush();
    }

    private function parsePriceInteger(string $value): ?int
    {
        $value = $this->parsePrice($value);
        if ($value) {
            return (int)$value;
        }
        return null;
    }

    private function parsePriceFloat(string $value): ?float
    {
        $value = $this->parsePrice($value);
        if ($value) {
            return (float)$value;
        }
        return null;
    }

    private function parsePrice(string $value): ?string
    {
        $value = explode('руб', $value)[0] ?? null;
        if ($value) {
            $value = trim($value);
            return join(explode(' ', $value));
        }
        return null;
    }
}