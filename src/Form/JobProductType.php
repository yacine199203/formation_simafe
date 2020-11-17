<?php

namespace App\Form;

use App\Entity\JobProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('job',EntityType::class,[
            'label'=>'Métier :',
            'class'=>'App\Entity\Job',
            'choice_label'=>'job',
            'choice_value'=>'id',
            'expanded'=>false,
            'multiple'=>false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JobProduct::class,
        ]);
    }
}
