<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\BrandRepository;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class ImportService
{
    public function __construct(
        protected ParameterBagInterface  $parameterBag,
        protected BrandRepository        $brandRepository,
        protected EntityManagerInterface $entityManager,
        protected CategoriesRepository   $categoriesRepository,
        protected ProductsRepository     $productsRepository,
        protected SerializerInterface    $serializer,
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
        $category = $this->categoriesRepository->findOneBy(['title' => 'Без категории']);
        $foundedProducts = [];
        $i = 12;
        for (; $i < count($data); $i++) {
            $csv = str_getcsv($data[$i], ';');
            if (($this->parsePriceFloat($csv[5]) == 0 and $this->parsePriceInteger($csv[1]) == 0) or count($csv) > 7) {
                $csvRev = array_reverse($csv);
                $csv = [
                    join(';', array_reverse(array_splice($csvRev, 6))),
                    $csvRev[5],
                    $csvRev[4],
                    $csvRev[3],
                    $csvRev[2],
                    $csvRev[1],
                ];
            }
            $name = trim($csv[0]);
            if (empty($csv[1])) {
                continue;
            }
            if (($indexName = array_search($name, $productNames)) !== false) {
                $product = $products[$indexName];
                $foundedProducts[] = $productIds[$indexName];
                if (!$product->getCategories() && $category) {
                    $product->setCategories($category);
                }
            } else {
                $product = new Products();
                if ($category) {
                    $product->setCategories($category);
                }
                $this->entityManager->persist($product);
            }
            $description =
                "Код: " . ($csv[2] ?? '-') . "<br>";

            $product
                ->setDescription($description)
                ->setPrice($this->parsePriceFloat($csv[5]))
                ->setBalanceStock($this->parsePriceInteger($csv[1]))
                ->setArticle($csv[3])
                ->setTitle($name)
                ->setCode1C($csv[2]);
            if ($csv[4] != '' && !empty($csv[4])) {
                if (($index = array_search($csv[4], $brandNames)) !== false) {
                    $product->setBrand($brands[$index]);
                } else {
                    $brand = (new Brand())->setName($csv[4]);
                    $brands[] = $brand;
                    $product->setBrand($brand);
                    $brandNames[] = $brand->getName();
                }
            }
        }
        $resIds = array_diff($productIds, $foundedProducts);
        $removedProducts = $this->productsRepository->findBy(['id' => $resIds]);
        foreach ($removedProducts as $product) {
            $this->entityManager->remove($product);
        }
        $this->entityManager->flush();
        shell_exec('php bin/console cache:clear');
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