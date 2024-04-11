<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duree', ChoiceType::class, [
                'label' => 'Durée du blocage',
                'choices' => [
                    '1 semaine' => '1 week',
                    '15 jours' => '15 days',
                    '1 mois' => '1 month',
                    '2 mois' => '2 months',
                    '3 mois' => '3 months',
                    '4 mois' => '4 months',
                    '6 mois' => '6 months',
                    '1 an' => '1 year'

                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
