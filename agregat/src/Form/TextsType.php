<?php

namespace App\Form;

use App\Entity\Texts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Helper\EnumType\TextsType as TextsTypeEnum;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['type'] == TextsTypeEnum::HOW_TO_BUY->value) {
            $builder
                ->add('title', options: ['label' => 'Название'])
                ->add('description', TextareaType::class, ['label' => 'Описание'])
                ->add('subType', ChoiceType::class, ['label' => 'Подтип']);
        } else if ($options['type'] == TextsTypeEnum::CONTACTS->value) {
            $builder->add('description', TextareaType::class, ['label' => 'Описание']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Texts::class,
            'type' => null
        ]);
    }
}
