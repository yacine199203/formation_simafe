<?php

namespace App\Form;

use App\Form\ConditionType;
use App\Entity\Recruitement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecruitementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('job',TextType::class,[
                'label'=>'Poste :',
                'attr'=>[
                    'placeholder'=>'Ex: Commercial'    
                ]
            ])
            ->add('city',TextType::class,[
                'label'=>'Ville :',
                'attr'=>[
                    'placeholder'=>'Ex: Alger'    
                ]
            ])
            ->add('conditions',CollectionType::class,
            [
                'label'=>'Conditions :',
                'entry_type' => ConditionType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recruitement::class,
        ]);
    }
}
