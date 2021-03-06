<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Type\RolesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserType extends AbstractType
{

    private $authorizationChecker;


    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker
    ){
        $this->authorizationChecker = $authorizationChecker;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Pseudonyme'])
            ->add('roles')
            //->add('password')
            ->add('email')
            ->add('avatar', FileType::class, [
                'label' => 'Avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image compatible, merci',
                    ])
                ],
            ])
            ->add('overview', TextareaType::class, [
                'label' => 'Petite description de vous',
                'required' => false,
            ]);

        if($options['style'] === 'register'){
            $builder->remove('roles')->remove('password')->remove('avatar')->remove('created_at');
            $builder->add('plainPassword', PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [new NotBlank()]
                ]);
                $builder->remove('overview');
                $builder->add('overview', TextareaType::class, [
                    'label' => 'Petite description de vous',
                    'required' => false
                ]);
                $builder->add(
                'submit',
                SubmitType::class,
                ['label' => 'S\'inscrire', 'attr' => ['class' => 'btn btn-success']]
            );
        }

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onPreSetData']
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm(); //récupération du formulaire

        /** @var $entity User */
        $entity = $event->getData(); //récupération de l'entité

        $entity->setCreatedAt(new \DateTime());

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')===true){
            $form->add('roles', RolesType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'style' => 'null',
            ]
        );
    }
}
