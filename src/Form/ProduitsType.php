<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Terrains;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('quantite', IntegerType::class, [
            'label' => 'Quantité',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'La quantité ne peut pas être vide.',
                ]),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'La quantité doit être un nombre entier.',
                ]),
                new Assert\GreaterThan([
                    'value' => 0,
                    'message' => 'La quantité doit être supérieure à zéro.',
                ])
            ],
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom du produit',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom du produit ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'max' => 90,
                    'maxMessage' => 'Le nom du produit ne peut pas dépasser {{ limit }} caractères.',
                ])
            ],
        ])
        ->add('prix', IntegerType::class, [
            'label' => 'Prix',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prix ne peut pas être vide.',
                ]),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'Le prix doit être un nombre entier.',
                ]),
                new Assert\GreaterThan([
                    'value' => 0,
                    'message' => 'Le prix doit être supérieur à zéro.',
                ])
            ],
        ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false, // Important : Ne pas lier directement à l'entité
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WebP)',
                    ])
                ],
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
