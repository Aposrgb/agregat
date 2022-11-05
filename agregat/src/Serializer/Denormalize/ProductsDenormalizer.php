<?php

namespace App\Serializer\Denormalize;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class ProductsDenormalizer implements ContextAwareDenormalizerInterface
{
    public function __construct(
        protected ProductsRepository $productsRepository
    )
    {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return new $type() instanceof Products and array_key_exists('denormalize', $context);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        if (method_exists($this, $context['denormalize'])) {
            $denormalize = $context['denormalize'];
            return $this->$denormalize($data, $type, $format, $context);
        }
        return null;
    }

    public function denormalizeXml($data, string $type, string $format = null, array $context = [])
    {
        $products = [];
        $allProducts = $this->productsRepository->findAll();
        if (is_array($data) && array_key_exists('employee', $data)) {
            $empls = $data['employee'];
            foreach ($empls as $item) {
                if (is_array($item)) {
                    $staff = $this->fillEntityFromExternalXml($item, $allProducts, $organization);
                    $allProducts[$item['employeeGUID']] = $staff;
                    $products[$item['employeeGUID']] = $staff;
                } else {
                    $staff = $this->fillEntityFromExternalXml($empls, $allProducts, $organization);
                    $allProducts[$item['employeeGUID']] = $staff;
                    $products[$item['employeeGUID']] = $staff;
                    break;
                }
            }
        }
        return $products;
    }

}