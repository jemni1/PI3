<?php
namespace App\Form;

use App\Entity\CollecteDechet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'required' => false,
                'scale' => 2, 
            ])
            ->add('dateCollecte', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de collecte',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollecteDechet::class,
        ]);
    }
}
