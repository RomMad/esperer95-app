<?php

namespace App\Form\Security;

use App\Form\Model\UserResetPassword;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username", null, [
                "label_attr" => [
                    "class" => "sr-only"
                ],
                "attr" => [
                    "placeholder" => "Login"
                ]
            ])
            ->add("email", null, [
                "label_attr" => [
                    "class" => "sr-only"
                ],
                "attr" => [
                    "placeholder" => "Email"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => UserResetPassword::class,
            "translation_domain" => "forms",
        ]);
    }
}
