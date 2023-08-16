<?php

namespace App\Form;

use App\Entity\GB;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class GBType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class)
                ->add('message', TinymceType::class, [
                    'attr' => [
                        "toolbar" => "bold italic underline | bullist numlist",
                        'plugins' => "advlist autolink link image media table lists",
                        'rows' => 10,
                    ],
                ])
                ->add('captcha', Recaptcha3Type::class, [
                    'constraints' => new Recaptcha3(),
                    'action_name' => 'new',
                    //'script_nonce_csp' => $nonceCSP,
                    'locale' => 'en',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GB::class,
        ]);
    }
}
