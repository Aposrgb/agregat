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
            ->add('name', null, ['label' => 'Название'])
            ->add('phone', null, ['label' => 'Телефон'])
            ->add('email')
            ->add('message', null, ['label' => 'Сообщение'])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Активный' => FeedbackStatus::ACTIVE->value,
                    'В прогрессе' => FeedbackStatus::IN_PROGRESS->value,
                    'Подтвержденный' => FeedbackStatus::CONFIRMED->value,
                ],
                'label' => 'Статус'
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
