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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
            ->add('numeroDeSiret', TextType::class, [
                'label' => 'Numero de Siret',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre numéro de Siret',
                    ]),
                    new Length([
                        'min' => 14,
                        'max' => 14,
                        'minMessage' => 'Le numéro de Siret doit comporter exactement {{ limit }} chiffres',
                        'maxMessage' => 'Le numéro de Siret doit comporter exactement {{ limit }} chiffres',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le numéro de Siret doit être composé uniquement de chiffres',
                    ]),
                ],
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
