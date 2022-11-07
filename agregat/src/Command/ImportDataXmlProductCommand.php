<?php

namespace App\Command;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDataXmlProductCommand extends Command
{
    public function __construct(
        protected ProductsRepository     $productsRepository,
        protected EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('import:xml:product')
            ->setDescription('Create products');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvFile = file(__DIR__ . '/data/cat_product_list.csv');
        foreach ($csvFile as $line) {
            $product = new Products();
            foreach (str_getcsv($line) as $index => $item) {
                if (empty($item)) {
                    continue;
                }
                switch ($index) {
                    case 0:
                        $product->setTitle($item);
                        break;
                    case 1:
                        $product->setId((int)$item);
                        break;
                    case 2:
                        $product->setImg($item);
                        break;
                    case 3:
                        $product->setDescription($item);
                        break;
                    case 4:
                        $product->setPrice((float)$item);
                        break;
                    case 5:
                        $product->setRating((float)$item);
                        break;
                }
            }
            $this->productsRepository->save($product);
        }
        $this->entityManager->flush();
        return Command::SUCCESS;
    }

}