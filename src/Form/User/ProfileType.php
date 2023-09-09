<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    PasswordType,
    RepeatedType,
    TextType,
};
use Symfony\Component\Validator\Constraints\{
    NotBlank,
    Length,
};
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{

    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, ['attr' => [
                        'class' => 'form-control',
                        'name' => 'sdfsdf',
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => [
                        'attr' => [
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'first_options' => [
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
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'label' => 'label.new_password',
                    ],
                    'second_options' => [
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'label' => 'label.repeat_password',
                        'label_attr' => [
                            'class' => 'mt-3',
                        ],
                    ],
                    'invalid_message' => 'The password fields must match.',
                    // Instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
        ]);
    }

    /**
     * 
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
