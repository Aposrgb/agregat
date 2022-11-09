<?php

namespace App\Form;

use App\Entity\Feedback;
use App\Helper\EnumStatus\FeedbackStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('message')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => FeedbackStatus::ACTIVE->value,
                    'In progress' => FeedbackStatus::IN_PROGRESS->value,
                    'Confirmed' => FeedbackStatus::CONFIRMED->value,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
