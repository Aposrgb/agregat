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
            ->add('firstname', options: ['label' => 'Имя'])
            ->add('surname', options: ['label' => 'Фамилия'])
            ->add('patronymic', options: ['label' => 'Отчество'])
            ->add('phone', options: ['label' => 'Телефон'])
            ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Админ' => UserRoles::ROLE_ADMIN->value,
                        'Пользователь' => UserRoles::ROLE_USER->value,
                    ],
                    'multiple' => true,
                    'mapped' => false,
                    'label' => 'Роль'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Подтвержденный' => UserStatus::CONFIRMED->value,
                    'Заблокирован' => UserStatus::BLOCKED->value
                ],
                'label' => 'Статус'
            ])
            ->add('email', EmailType::class, options: ['label' => 'E-Mail'])
            ->add('password', options: ['label' => 'Пароль']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
