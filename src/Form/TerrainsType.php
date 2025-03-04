<?php

namespace App\Form;

use App\Entity\Terrains;
use App\Entity\Culture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
class TerrainsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom du terrain'
        ])
        ->add('adresse', TextType::class, [
            'label' => 'Adresse'
        ])
        ->add('superficie', IntegerType::class, [
            'label' => 'Superficie (m²)'
        ])
       
        ->add('image', FileType::class, [
            'label' => 'Image du terrain',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => ['image/jpeg', 'image/png'],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG ou PNG)'
                ])
            ],
        ])
        ->add('cultures', EntityType::class, [
            'class' => Culture::class,
            'choice_label' => 'nom',
            'multiple' => true,
            'required' => false,
            'attr' => [
                'class' => 'select2-cultures',
                'data-placeholder' => 'Rechercher des cultures...'
            ]
        ])
        ->add('latitude',  NumberType::class, [
            'attr' => [
                'class' => 'map-latitude',
                'data-field' => 'latitude'
            ],
            'required' => true
        ])
        ->add('longitude',  NumberType::class, [
            'attr' => [
                'class' => 'map-longitude',
                'data-field' => 'longitude'
            ],
            'required' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrains::class,
        ]);
    }
}
