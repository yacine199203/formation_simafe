<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('userRoles',ChoiceType::class,[
            'label'=>'Rôle :',
            'choices'  => [
                'Administrateur' => 'Administrateur',
                'Commercial' => 'Commercial',
                'Client' => 'Client',
            ],
            'expanded'=>true,
            'multiple'=>true,
        ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
