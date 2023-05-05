<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextareaType::class, ['label' => 'Название'])
            ->add('img', FileType::class, ['mapped' => false, 'label' => 'Изображение'])
            ->add('rating', options: ['label' => 'Рейтинг'])
            ->add('description', TextareaType::class, ['label' => 'Описание'])
            ->add('isActual', options: ['label' => 'Является актуальным'])
            ->add('createdAt', options: ['label' => 'Дата создания'])
            ->add('price', options: ['label' => 'Цена'])
            ->add('balanceStock', NumberType::class, [
                'attr' => ['min' => 1],
                'label' => 'Остаток на складе'
            ])
            ->add('discountPrice', null, ['label' => 'Цена со скидкой'])
            ->add('code1C', options: ['label' => 'Код 1С'])
            ->add('purchaseBalance', options: ['label' => 'Остаток покупок'])
            ->add('keyWords', options: ['label' => 'Ключевые слова'])
            ->add('article', options: ['label' => 'Артикул'])
            ->add('categories',  NumberType::class,options: ['label' => 'Категория', 'mapped' => false])
            ->add('subcategories', NumberType::class, options: ['label' => 'Подкатегория', 'mapped' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
