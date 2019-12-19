<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TheRadioType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

        $choices = [
            "Serie" => "Serie",
            "Episode" => "Episode",
            "Categorie" => "Categorie"
        ];

        $resolver->setDefaults([
            'choices' => $choices,
            'multiple' => false,
            'expanded' => false,
        ]);
    }


    public function getParent(){
        return ChoiceType::class;
    }
}