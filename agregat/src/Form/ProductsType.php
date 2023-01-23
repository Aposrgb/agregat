<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextareaType::class, [
                'attr' => ['style' => 'width:500px']
            ])
            ->add('img', FileType::class, ['mapped' => false])
            ->add('rating')
            ->add('description', TextareaType::class, [
                'attr' => ['style' => 'width:500px']
            ])
            ->add('isActual')
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
