<?php

namespace App\Command;

use App\Entity\Brand;
use App\Entity\Products;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportProductsFromCSV extends Command
{
    public function __construct(
        protected ParameterBagInterface  $parameterBag,
        protected BrandRepository        $brandRepository,
        protected EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('import:product:csv');
    }

    /**
     * 0 => '',
     * 1 => 'Ценовая группа/ Номенклатура',
     * 2 => 'Остаток',
     * 3 => 'Номенклатура.Код',
     * 4 => 'Номенклатура.Артикул',
     * 5 => 'Номенклатура.Бренд',
     * 6 => 'Интернет',
     * 7 => '',
     * 8 => 'Основная цена продажи',
     * 9 => ''
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = file_get_contents($this->parameterBag->get('APP_DIRECTORY') . '/data/products.csv');
        $data = str_getcsv($data, "\n");
        $brands = $this->brandRepository->findAll();
        $brandNames = array_map(function (Brand $brand) {
            return $brand->getName();
        }, $brands);
        $i = 12;
        for ($i; $i < count($data); $i++) {
            $product = new Products();
            $csv = str_getcsv($data[$i], ';');
            if ($csv[5] != '' && !empty($csv[5])) {
                if ($index = array_search($csv[5], $brandNames)) {
                    $product->setBrand($brands[$index]);
                } else {
                    $product->setBrand((new Brand())->setName($csv[5]));
                }
            }
            $description =
                "Номенклатура.Код: " . ($csv[3] ?? '-') . "\n" .
                "Номенклатура.Артикул: " . ($csv[4] ?? '-') . "\n" .
                "Интернет: " . ($csv[6] ?? '-') . "\n";

            $this->entityManager->persist(
                $product
                    ->setPrice($this->parsePriceFloat($csv[8]))
                    ->setTitle($csv[1])
                    ->setBalanceStock($this->parsePriceInteger($csv[2]))
                    ->setDescription($description)
            );
        }
        $this->entityManager->flush();
        return Command::SUCCESS;
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