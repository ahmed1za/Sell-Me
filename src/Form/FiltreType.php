<?php

namespace App\Form;

use App\Data\Filtre;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class FiltreType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('min',NumberType::class,[
            'label'=>false,
            'required'=>false,
            'attr'=>[
                'placeholder'=>'Prix min'
            ]
        ])
        ->add('max',NumberType::class,[
            'label'=>false,
            'required'=>false,
            'attr'=>[
                'placeholder'=>'Prix max'
            ]
        ])


        ->add('categories',EntityType::class,[
        'class'=>Categories::class,
        'choice_label'=> 'nom',
        'multiple'=>false,
        'required'=>false,
        'placeholder'=>'choisir une categorie'
    ])

        ->add('livraison',ChoiceType::class,[
            "choices"=>[
                'Oui'=>true,
                'Non'=> false
            ],
            'label'=>'livraison',
            'required'=>false,
        ])
        ->add('nature',ChoiceType::class,[
            "choices"=>[

                'professionnel'=>'professionnel',
                'particulier'=> 'particulier'
            ],
            'label'=> "Annonce publier par :",
            'placeholder'=>"Statut",
            'required'=>false

        ])
        ->add('etat',ChoiceType::class,[
            'choices'=>[
                'Neuf'=>'Neuf',
                'Occasion'=>'Occasion'
            ],
            'label'=>false,
            'placeholder'=>'état',
            'required'=>false


        ])

    ;

}
    public function configureOptions(OptionsResolver $resolver)
    {
           $resolver->setDefaults([
               'data_class'=> Filtre::class,
               'method'=>'GET',
               'csrf_protection'=>false,
           ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}