<?php

namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("lastname", NULL, [
            "label" => "Nom",
            "attr" => [
                "placeholder" => "Nom"
            ]
        ])
        ->add("firstname", NULL, [
            "label" => "Prénom",
            "attr" => [
                "placeholder" => "Prénom"
            ]
        ])
        ->add("birthdate", DateType::class, [
            "label" => "Date de naissance",
            "widget" => "single_text",
            "attr" => [
                "class" => "col-md-6"
            ],
            "required" => false
            
        ])
        ->add("gender", ChoiceType::class, [
            "label" => "Sexe",
            "attr" => [
                "class" => "col-md-6"
            ],
            // "placeholder" => "Sélectionner une option",
            "choices" => [
                "-- Sélectionner --" => NULL,
                "Femme" => "1",
                "Homme" => "2",
                "Autre" => "3"
            ],
        ])
        ->add("comment",NULL, [
            "label" => "Commentaire",
            "attr" => [
                "rows" => 4,
                "placeholder" => "Saisir un commentaire sur la personne"
            ]
        ])
        // ->add("creationDate", DateTimeType::class, [
        //     "widget" => "single_text",
        //     "format" => "dd/MM/YYY H:m",
        // ])                    
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Person::class,
        ]);
    }
}