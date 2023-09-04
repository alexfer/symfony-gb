<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType,
    PasswordType,
    TextType,
    EmailType,
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{
    IsTrue,
    Length,
    NotBlank,
};

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
                    'attr' => [
                        'placeholder' => 'Jhohn Smith',
                        'required' => 'required'
                    ],
                ])
                ->add('email', EmailType::class, [
                    'attr' => [
                        'placeholder' => 'Your email address',
                    ],
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'You should agree to our terms.',
                                ]),
                    ],
                ])
                ->add('plainPassword', PasswordType::class, [
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                                ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                                ]),
                    ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
