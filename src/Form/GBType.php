<?php

namespace App\Form;

use App\Entity\GB;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Gregwar\CaptchaBundle\Type\CaptchaType;

class GBType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class)
                ->add('message', CKEditorType::class);

//        $builder->add('captcha', CaptchaType::class, [
//            'label_attr' => [
//                'class' => 'form-group mt-4 mb-4',
//                'for' => "captcha",
//        ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GB::class,
        ]);
    }
}
