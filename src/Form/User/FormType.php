<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    PasswordType,
    CheckboxType,
    ChoiceType,
};
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Validator\Constraints\{
    NotBlank,
    Length,
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class FormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name')
                ->add('email')
                ->add('password', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                                ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                                ]),
                    ],
                ])
                ->add('roles', ChoiceType::class, [
                    'label' => 'label.role',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'User' => 'ROLE_USER',
                    ],
                ])
                ->add('is_verified', CheckboxType::class, [
                    'label' => 'Verified?',
                    'label_attr' => [
                        'class' => 'form-check-label'
                    ],
                    'required' => false,
                ])
                ->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                                function ($rolesArray) {
                                    return count($rolesArray) ? $rolesArray[0] : null;
                                },
                                function ($rolesString) {
                                    return [$rolesString];
                                }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
