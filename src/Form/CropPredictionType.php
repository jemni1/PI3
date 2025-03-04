<?php
// src/Form/CropPredictionType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CropPredictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nitrogen', NumberType::class, [
                'label' => 'Nitrogen (N)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 140,
                    'step' => '0.1'
                ],
                'required' => true,
            ])
            ->add('phosphorus', NumberType::class, [
                'label' => 'Phosphorus (P)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 140,
                    'step' => '0.1'
                ],
                'required' => true,
            ])
            ->add('potassium', NumberType::class, [
                'label' => 'Potassium (K)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 140,
                    'step' => '0.1'
                ],
                'required' => true,
            ])
            ->add('temperature', NumberType::class, [
                'label' => 'Temperature (°C)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 50,
                    'step' => '0.1'
                ],
                'required' => true,
            ])
            ->add('humidity', NumberType::class, [
                'label' => 'Humidity (%)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 100,
                    'step' => '0.1'
                ],
                'required' => true,
            ])
            ->add('ph', NumberType::class, [
                'label' => 'pH',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 14,
                    'step' => '0.1'
                ],
                'required' => true,
            ])
            ->add('rainfall', NumberType::class, [
                'label' => 'Rainfall (mm)',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 300,
                    'step' => '0.1'
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}