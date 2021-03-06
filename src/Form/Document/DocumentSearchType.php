<?php

namespace App\Form\Document;

use App\Entity\Document;
use App\Form\Model\DocumentSearch;

use App\Form\Utils\Choices;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DocumentSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", null, [
                "label_attr" => ["class" => "sr-only"],
                "attr" => [
                    "placeholder" => "Search"
                ]
            ])
            ->add("type", ChoiceType::class, [
                "label_attr" => ["class" => "sr-only"],
                "choices" => Choices::getchoices(Document::TYPE),
                "attr" => [
                    "class" => "w-max-150",
                ],
                "placeholder" => "-- Type --",
                "required" => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => DocumentSearch::class,
            "translation_domain" => "forms"
        ]);
    }
}
