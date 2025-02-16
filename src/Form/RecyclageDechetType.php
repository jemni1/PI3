<?php

namespace App\Form;

use App\Entity\RecyclageDechet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\CollecteDechet;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajouter l'importation de FileType

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
                'attr' => ['class' => 'form-select'], 
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('collectes', EntityType::class, [
                'class' => CollecteDechet::class,
                'choice_label' => function ($collecte) {
                    return $collecte->getTypeDechet() . ' - ' . $collecte->getQuantite() . 'kg (' . $collecte->getDateDebut()->format('d/m/Y') . ')';
                },
                'multiple' => true,
                'expanded' => true, 
                'by_reference' => false, 
            ])
            ->add('image', FileType::class, [
                'label' => 'Image recyclage',
                'required' => false,
                'mapped' => false, // Ce champ n'est pas lié directement à la propriété de l'entité
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
