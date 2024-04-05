<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('nature', ChoiceType::class, [
                'choices' => [
                    'Un particulier' => 'particulier',
                    'Un professionnel' => 'professionnel',
                ],

                'multiple' => false,
                'label' => 'vous êtes :',
            ])
            ->add('adress',TextType::class)
            ->add('codePostal', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 99999,
                ],
                'label'=>'Code postal'
            ])
            ->add('Ville')
            ->add('Pays')
            ->add('photoDeProfil', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'multiple'=>false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
