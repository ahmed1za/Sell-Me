<?php

namespace App\Form;

use App\Entity\Signalisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motif',ChoiceType::class,[
                "choices"=>[
                   "Comportement offensant"=>"Comportement offensant",
                    "Comportement suspect"=>"Comportement suspect",
                    "Demarcheur/Spam"=>"Demarcheur/Spam"
                ]
            ])


            ->add('raison',ChoiceType::class,[
                "choices"=>[
                    "Menaces"=>"Menaces",
                    "discour haineux"=> "discour haineux",
                    " Intimidation"=>" Intimidation",
                    "Harcèlement"=>"Harcèlement",
                    "Fraude"=>"Fraude",
                    "Arnaque"=>"Arnaque",
                    "Spammeur"=>"Spammeur"
                ]
            ])
            ->add('description',TextareaType::class,[
                'label'=>"Dites nous en plus (facultatif)",
                'required'=>false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signalisation::class,
        ]);
    }
}
