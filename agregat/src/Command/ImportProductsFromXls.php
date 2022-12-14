<?php

namespace App\Command;

use App\Entity\Brand;
use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\SubCategories;
use App\Repository\BrandRepository;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Repository\SubCategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportProductsFromXls extends Command
{
    /** @var Products[] */
    private array $products;
    /** @var Brand[] */
    private array $brand;
    /** @var Categories[] */
    private array $categories;
    /** @var SubCategories[] */
    private array $subCategories;

    private array $subCategoryNames;
    private array $categoryNames;
    private array $productNames;
    private array $brandNames;

    public function __construct(
        protected ProductsRepository      $productsRepository,
        protected EntityManagerInterface  $entityManager,
        protected ParameterBagInterface   $parameterBag,
        protected BrandRepository         $brandRepository,
        protected CategoriesRepository    $categoriesRepository,
        protected SubCategoriesRepository $subCategoriesRepository,
    )
    {
        parent::__construct();
        $this->products = $this->productsRepository->findAll();
        $this->brand = $this->brandRepository->findAll();
        $this->categories = $this->categoriesRepository->findAll();
        $this->subCategories = $this->subCategoriesRepository->findAll();
        $this->setNamesEntity(true, true, true, true);
    }

    protected function configure()
    {
        $this
            ->setName('import:xls:product')
            ->setDescription('Create products');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $xlsFilesPath = $this->parameterBag->get('PUBLIC_DIRECTORY') . '/xls/';
        $files = $this->scanDir($xlsFilesPath, $this->getDirAndFiles($xlsFilesPath));
        $this->getClassByFiles($xlsFilesPath, $files);
        return Command::SUCCESS;
    }

    private function getClassByFiles(string $path, array $categoryFiles)
    {
        foreach ($categoryFiles as $name => $files) {
            $index = array_search($name, $this->categoryNames);
            if ($index !== false) {
                $categories = $this->categories[$index];
            } else {
                $categories = (new Categories())->setTitle($name);
                $this->categories[] = $categories;
                $this->setNamesEntity(category: true);
            }
            $this->categoriesRepository->save($categories);
            foreach ($files as $j => $file) {
                if (!is_numeric($j)) {
                    $subCategories = $file;
                    $subName = $j;
                    foreach ($subCategories as $subCategoryFile) {
                        $index = array_search($subName, $this->subCategoryNames);
                        if ($index !== false) {
                            $subCategory = $this->subCategories[$index];
                        } else {
                            $subCategory = (new SubCategories())->setTitle($subName);
                            $this->subCategories[] = $subCategory;
                            $this->setNamesEntity(sub: true);
                            $subCategory->setCategory($categories);
                            $this->subCategoriesRepository->save($subCategory);
                        }
                        $this->product("$path$name/$subName/$subCategoryFile", $categories, $subCategory);
                    }
                } else {
                    $this->product("$path$name/$file", $categories);
                }
            }
        }
        $this->entityManager->flush();
    }

    private function scanDir($path, array $scanDir): string|array
    {
        $files = [];
        foreach ($scanDir as $scItem) {
            if (is_dir($path . $scItem)) {
                $files[$scItem] = $this->scanDir(
                    $path . $scItem . '/', $this->getDirAndFiles($path . $scItem)
                );
                continue;
            }
            $files[] = $scItem;
        }
        return $files;
    }

    private function getDirAndFiles($path): array
    {
        return array_diff(scandir($path), array('..', '.'));
    }

    private function product(string $path, Categories $categories, ?SubCategories $subCategories = null): void
    {
        $spreadsheet = IOFactory::load($path);
        $spreadsheet = $spreadsheet->getActiveSheet();
        $data = $spreadsheet->toArray();
        $i = 0;
        $columns = $data[$i];
        $i++;
        for (; $i < count($data); $i++) {
            $row = $data[$i];
            $brand = null;
            if ($row[6]) {
                $index = array_search($row[6], $this->brandNames);
                if ($index !== false) {
                    $brand = $this->brand[$index];
                } else {
                    $brand = new Brand();
                    $brand->setName($row[6]);
                    $this->brand[] = $brand;
                    $this->setNamesEntity(brand: true);
                }
            }
            $index = array_search($row[23], $this->productNames);
            if ($index !== false) {
                $product = $this->products[$index];
            } else {
                $product = new Products();
                $product->setTitle($row[23]);
                $this->products[] = $product;
                $this->setNamesEntity(product: true);
            }
            $product
                ->setCategories($categories->addProduct($product))
                ->setSubCategories($subCategories?->addProduct($product))
                ->setKeyWords($row[0])
                ->setIsActual((bool)$row[5])
                ->setBrand($brand)
                ->setCode1C($row[8] ?? null)
                ->setDescription(
                    $this->getDescriptionByProperty(
                        1, 23, $columns, $row, [5, 6, 8]
                    )
                );
            $this->productsRepository->save($product);
        }
    }

    private function getDescriptionByProperty(int $start, int $end, array $columns, array $propertys, array $unUsePropertys): string
    {
        $desc = '';
        for (; $start < $end; $start++) {
            if (!in_array($start, $unUsePropertys)) {
                $property = $this->getPropertyNotNull($columns[$start], $propertys[$start]);
                if ($property) {
                    $desc .= $property . "\n";
                }
            }
        }
        return $desc;
    }

    private function getPropertyNotNull(string $name, string|int|null $property): ?string
    {
        if (!is_null($property) && $property != 0) {
            return $name . ': ' . $property;
        }
        return null;
    }

    private function setNamesEntity(bool $brand = false, bool $product = false, bool $sub = false, bool $category = false): void
    {
        if ($brand) {
            $this->brandNames = array_map(function (Brand $brand) {
                return $brand->getName();
            }, $this->brand);
        }
        if ($product) {
            $this->productNames = array_map(function (Products $products) {
                return $products->getTitle();
            }, $this->products);
        }
        if ($sub) {
            $this->subCategoryNames = array_map(function (SubCategories $subCategory) {
                return $subCategory->getTitle();
            }, $this->subCategories);
        }
        if ($category) {
            $this->categoryNames = array_map(function (Categories $category) {
                return $category->getTitle();
            }, $this->categories);
        }
    }

}