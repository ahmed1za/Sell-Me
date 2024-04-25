<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'label'=>'titre'
            ])
            ->add('discription', TextareaType::class,[
                'label'=>"Description"
            ])
            ->add('etat', ChoiceType::class,[
                'choices' => [
                    'Neuf'=>'Neuf',
                    'Occasion'=>'Occasion'
    ],
                'multiple'=>false
        ])
            ->add('prix')
            ->add('quantite')
           /* ->add('categories', ChoiceType::class,[
                'choices'=>[
                    'Informatique'=>'Informatique',
                    'Maison & cuisine'=>'Maison & cuisine',
                    'Sport'=>'Sport',
                    'Jardin & animalerie'=>'Jardin & animalerie',
                    'Elecromenager'=>'Elecromenager',
                    'Bijoux'=>'Bijoux',
                    'Vehicule'=>'Vehicule'
                ]
            ])*/
            ->add('image',FileType::class,[
        'label'=>false,
        'multiple'=>false,
               'attr' => [
                   'accept' => 'image/*',
               ],
        'mapped'=>false,
        'required'=>false
    ])
            ->add('livraison',CheckboxType::class,[
                'label'=>'Livraison disponible ?',
                'required'=>false
            ])
            ->add('categorie',EntityType::class,[
                'class'=>Categories::class,
                'choice_label'=> 'nom'
            ])

                      ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
