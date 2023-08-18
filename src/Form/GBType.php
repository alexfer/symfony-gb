<?php

namespace App\Form;

use App\Entity\GB;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class GBType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder//->add('name', TextType::class)
                //->add('email', EmailType::class)
                ->add('title', TextType::class)
                ->add('message', TinymceType::class, [
                    'attr' => [
                        "toolbar" => "bold italic underline | bullist numlist",
                        'plugins' => "advlist autolink link image media table lists",
                        'rows' => 10,
                    ],
                ])
                ->add('captcha', CaptchaType::class, [
                    'label_attr' => [
                        'class' => 'form-group mt-4 mb-4',
                        'for' => "captcha",
        ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GB::class,
        ]);
    }
}
