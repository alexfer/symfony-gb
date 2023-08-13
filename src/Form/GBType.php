<?php

namespace App\Form;

use App\Entity\GB;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Uid\Uuid;

class GBType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('title')
                ->add('message')
                ->add('uuid', HiddenType::class, [
                    'data' => Uuid::v4(),
                ])
                ->add('author_id', HiddenType::class, [
                    'data' => '1',
                ])
        //->add('created_at')
        //->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GB::class,
        ]);
    }
}
