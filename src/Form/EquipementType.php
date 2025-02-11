<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Equipement;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom de l\'équipement',
            'attr' => ['placeholder' => 'Saisissez le nom...'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le nom est obligatoire.',
                ]),
            ],
        ])
        ->add('type', TextType::class, [
            'label' => 'Type d\'équipement',
            'attr' => ['placeholder' => 'Ex: Ordinateur, Imprimante...'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le type est obligatoire.',
                ]),
            ],
        ])
 ->add('dateAchat', DateType::class, [
    'widget' => 'single_text',
    'label' => 'Date d\'achat',
    'constraints' => [
        new NotBlank([
            'message' => 'La date d\'achat est obligatoire.',
        ]),
      
    ],
])

        ->add('etat', ChoiceType::class, [
            'label' => 'État',
            'choices' => [
                'Neuf' => 'neuf',
                'Bon état' => 'bon_etat',
                'Usé' => 'use',
                'Hors service' => 'hors_service',
            ],
            'placeholder' => 'Sélectionnez un état',
        ])
        ->add('dateDernierEntretien', DateType::class, [
            'label' => 'Date du dernier entretien',
            'widget' => 'single_text',
            'required' => false,
            'attr' => ['placeholder' => 'Sélectionnez une date'],
        ]);
        
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}
