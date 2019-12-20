<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',TextareaType::class,[
                'label' => ' ',
                'constraints'=>[
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => "Votre message doit contenir au moins 5 caractères",
                        "maxMessage" => "Votre message doit contenir au plus 255 caractères"
                    ]),
                    new NotBlank([
                        'message' => 'Votre message ne doit pas être vide'
                    ])
                ]
            ])
            //->add('positive')
            //->add('validated')
            //->add('created_at')
            //->add('User')
            //->add('Series')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
