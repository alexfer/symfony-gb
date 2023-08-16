<?php

namespace App\Form;

use App\Entity\GB;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    TextareaType,
    TextType,
};
use Eckinox\TinymceBundle\Form\Type\TinymceType;

class GBType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('title', TextType::class)
                ->add('message', TinymceType::class, [
                    'attr' => [
                        "toolbar" => "bold italic underline | bullist numlist",
                        'plugins' => "advlist autolink link image media table lists",
                        'rows' => 10,
                    ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GB::class,
        ]);
    }
}
