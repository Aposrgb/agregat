<?php

namespace App\Form;

use App\Entity\SubCategories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubCategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('img', FileType::class, ['mapped' => false, 'label' => 'Изображение'])
            ->add('title', options: ['label' => 'Название'])
            ->add('description', TextareaType::class, ['label' => 'Описание'])
            ->add('categories', null, ['mapped' => false, 'label' => 'Категория']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubCategories::class,
        ]);
    }
}
