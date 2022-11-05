<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('img')
            ->add('rating')
            ->add('description')
            ->add('isPopular')
            ->add('isAvailable')
            ->add('isRecommend')
            ->add('isActual')
            ->add('isNew')
            ->add('createdAt')
            ->add('price')
            ->add('discountPrice')
            ->add('categories')
            ->add('favoriteUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
