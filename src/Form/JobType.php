<?php

namespace App\Form;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('job',TextType::class,[
                'label'=>'Métier :',
                'attr'=>[
                    'placeholder'=>'Ex: Boucherie'    
                ]
            ])
            ->add('image',FileType::class,[
                'label'=>'Image (uniquement en format png) :',
                'data_class' => null,
                'attr'=>[
                    'accept'=>'.png', 
                    'placeholder'=>'Ex: image.png', 
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
