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
        $brandNames = array_map(function (Brand $brand) {
            return $brand->getName();
        }, $brands);
        $products = $this->productsRepository->findAll();
        $productNames = array_map(function (Products $products) {
            return $products->getTitle();
        }, $products);
        $i = 12;
        for ($i; $i < count($data); $i++) {
            $csv = str_getcsv($data[$i], ';');
            if ($indexName = array_search($csv[1], $productNames)) {
                $product = $products[$indexName];
            } else {
                $description =
                    "Номенклатура.Код: " . ($csv[3] ?? '-') . "\n" .
                    "Номенклатура.Артикул: " . ($csv[4] ?? '-') . "\n";

                $product = (new Products())
                    ->setBalanceStock($this->parsePriceInteger($csv[2]))
                    ->setDescription($description);
            }
            if ($csv[5] != '' && !empty($csv[5])) {
                if ($index = array_search($csv[5], $brandNames)) {
                    $product->setBrand($brands[$index]);
                } else {
                    $product->setBrand((new Brand())->setName($csv[5]));
                }
            }

            $this->entityManager->persist($product->setPrice($this->parsePriceFloat($csv[6])));
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