<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
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
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
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
            ->add('ville', TextType::class)
            ->add('pays', TextType::class)
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
