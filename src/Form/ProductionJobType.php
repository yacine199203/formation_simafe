<?php

namespace App\Form;

use App\Entity\ProductionJob;
use App\Form\ProductionJobType;
use App\Form\ProductionImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductionJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer',TextType::class,[
                'label'=>'Client :',
                'attr'=>[
                    'placeholder'=>'Nom ou raison sociale'    
                ]
            ])
            ->add('job',EntityType::class,[
                'label'=>'Métier :',
                'class'=>'App\Entity\Job',
                'choice_label'=>'job',
                'choice_value'=>'id',
                'expanded'=>false,
                'multiple'=>false,
            ])
            ->add('image',FileType::class,[
                'label'=>'Image (uniquement en format png) :',
                'attr'=>[
                    'accept'=>'.png', 
                    'placeholder'=>'Ex: image.png', 
                ],
                'mapped'=> false,
                'required'=> false,
                'multiple'=> true,
            ])
            ->add('description',HiddenType::class,[
                'mapped'=> false,
                'required'=> false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductionJob::class,
        ]);
    }
}
