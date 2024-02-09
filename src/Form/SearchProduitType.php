<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     $builder   ->add('nom', TextType::class, [
        'label' => false,
        'required' => false,
        'attr' => [
            'placeholder' => 'Rechercher ...',
        ],
    ])
         ->add('categorie', EntityType::class, [
             'class' => Categories::class,
             'choice_label' => 'nom',
             'attr' => [
                 'placeholder' => 'Catégories',

             ],
             'placeholder' => 'Catégories',
             'label'=>false,
             'required'=>false

        ]);
    }

}