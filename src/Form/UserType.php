<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,[
                'label'=>'Prénom :',
                'attr'=>[
                    'placeholder'=>'Prénom'    
                ]
            ])
            ->add('lastName',TextType::class,[
                'label'=>'Nom :',
                'attr'=>[
                    'placeholder'=>'Nom'    
                ]
            ])
            ->add('email',EmailType::class,[
                'label'=>'Email :',
                'attr'=>[
                    'placeholder'=>'exemple@email.com'    
                ]
            ])
            ->add('pass',PasswordType::class,[
                'label'=>'Mot de passe :',
                'attr'=>[
                    'placeholder'=>'Mot de passe'    
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}