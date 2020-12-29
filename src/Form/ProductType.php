<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\JobProductType;
use App\Form\CharacteristicsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productName',TextType::class,[
                'label'=>'Produit :',
                'attr'=>[
                    'placeholder'=>'Ex: Sigma'    
                ]
            ])
            ->add('png',FileType::class,[
                'label'=>'Image de présentation (uniquement en format png) :',
                'required' => false,
                'mapped'=> false,
                'attr'=>[
                    'accept'=>'.png', 
                    'placeholder'=>'Ex: image.png', 
                ]
            ])
            ->add('pdf',FileType::class,[
                'label'=>'Fiche téchnique (uniquement en format pdf) :',
                'required' => false,
                'mapped'=> false,
                'attr'=>[
                    'accept'=>'.pdf', 
                    'placeholder'=>'Ex: fichier.pdf', 
                ]
            ])
            ->add('image',FileType::class,[
                'label'=>'Image (uniquement en format png) :',
                'required' => false,
                'mapped'=> false,
                'attr'=>[
                    'accept'=>'.png', 
                    'placeholder'=>'Ex: image.png', 
                ]
            ])
            ->add('category',EntityType::class,[
                'label'=>'Catégorie :',
                'class'=>'App\Entity\Category',
                'choice_label'=>'categoryName',
                'choice_value'=>'id',
                'expanded'=>false,
                'multiple'=>false,
            ])
            ->add('jobProducts',EntityType::class,[
                'label'=>'Métier :',
                'class'=>'App\Entity\Job',
                'choice_label'=>'job',
                'choice_value'=>'id',
                'expanded'=>true,
                'multiple'=>true,
                'mapped'=>false,
            ])
            ->add('characteristics',CollectionType::class,
            [
                'label'=>'Caractéristiques :',
                'entry_type' => CharacteristicsType::class,
                'allow_add' => true,
                'allow_delete' => true,
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
            'data_class' => Product::class,
        ]);
    }
}
