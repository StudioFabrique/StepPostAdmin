<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['addUser']) {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                    ],
                ])

                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                    ],
                ])
                ->add('fonction', TextType::class, [
                    'label' => 'Fonction',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                    ],
                ])
                ->add('password', PasswordType::class, [
                    'label' => 'Mot de passe',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                        'placeholder' => '***************** '
                    ],
                ]);
        } else if ($options['editPassword']) {
            $builder
                ->add('password', PasswordType::class, [
                    'label' => 'Mot de passe',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                        'placeholder' => '***************** '
                    ],
                ]);
        } else {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                    ],
                ])

                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                    ],
                ])
                ->add('fonction', TextType::class, [
                    'label' => 'Fonction',
                    'label_attr' => ['class' => 'block text-gray-700 text-sm font-bold mb-2'],
                    'attr' => [
                        'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'editPassword' => false,
            'addUser' => false
        ]);
    }
}
