<?php

namespace App\Form;

use App\Entity\User;
use App\Helper\EnumRoles\UserRoles;
use App\Helper\EnumStatus\UserStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('surname')
            ->add('patronymic')
            ->add('phone')
            ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => UserRoles::ROLE_ADMIN->value,
                        'User' => UserRoles::ROLE_USER->value,
                    ],
                    'multiple' => true,
                    'mapped' => false
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Confirmed' => UserStatus::CONFIRMED->value,
                    'Blocked' => UserStatus::BLOCKED->value
                ]
            ])
            ->add('email', EmailType::class)
            ->add('password');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
