<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType; 
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajouter l'importation de FileType
use App\Entity\CollecteDechet;

class CollecteDechetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeDechet', ChoiceType::class, [
                'label' => 'Type de déchet',
                'choices' => [
                    'Fumier' => 'fumier',
                    'Paille' => 'paille',
                    'Déchets végétaux' => 'dechets_vegetaux',
                    'Coques de fruits' => 'coques_fruits',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionner un type',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité (kg)',
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de la collecte',
                'required' => false,
                'mapped' => false, // Ce champ n'est pas lié directement à la propriété de l'entité
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollecteDechet::class,
        ]);
    }
}
