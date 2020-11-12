<?php

namespace App\Form;

use App\Entity\Sliders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SlidersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('image',FileType::class,[
            'label'=>'image (uniquement en format png) :',
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
            'data_class' => Sliders::class,
        ]);
    }
}
