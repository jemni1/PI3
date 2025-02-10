<?php

namespace App\Form;

use App\Entity\RecyclageDechet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecyclageDechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantiteRecyclee', NumberType::class, [
                'label' => 'Quantité recyclée (kg)',
                'attr' => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('energieProduite', NumberType::class, [
                'label' => 'Énergie produite (kWh)',
                'attr' => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('utilisation', ChoiceType::class, [
                'label' => 'Utilisation de l\'énergie',
                'choices' => [
                    'Irrigation' => 'irrigation',
                    'Chauffage' => 'chauffage',
                    'Alimentation' => 'alimentation',
                    'Stockage d’énergie' => 'stockage',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionner un type',
                'attr' => ['class' => 'form-select'], // Style Bootstrap
               
            ])
            ->add('dateRecyclage', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de recyclage',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecyclageDechet::class,
        ]);
    }
}
