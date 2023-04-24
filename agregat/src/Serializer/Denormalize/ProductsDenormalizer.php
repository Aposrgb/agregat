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

}