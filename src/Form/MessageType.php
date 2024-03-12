<?php

namespace App\Form;

use App\Entity\Messages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('message',TextareaType::class,[
                'label'=>false,
                'required'=>false ,
                'attr'=>[
                    'placeholder'=>'Ecrivez votre message',
                    'id'=>'formchat-message'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'multiple'=>false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('fichier', FileType::class, [
                'label' => 'Fichier (PDF, Word, Excel)',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => '.pdf,.doc,.docx,.xls,.xlsx'],
            ])
            ->add('submit',ButtonType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
