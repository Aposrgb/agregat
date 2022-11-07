<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextareaType::class)
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
            ->add('code1C')
            ->add('balanceStock')
            ->add('purchaseBalance')
            ->add('keyWords')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
