<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Terrains;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
class ProduiteditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('quantite', IntegerType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'La quantité ne peut pas être vide.']),
                new Assert\Type(['type' => 'integer', 'message' => 'La quantité doit être un entier.']),
                new Assert\GreaterThan(['value' => 0, 'message' => 'La quantité doit être supérieure à zéro.']),
            ]
        ])
        ->add('nom', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le nom du produit ne peut pas être vide.']),
                new Assert\Length(['max' => 90, 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.']),
            ]
        ])
        ->add('prix', IntegerType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le prix ne peut pas être vide.']),
                new Assert\Type(['type' => 'integer', 'message' => 'Le prix doit être un entier.']),
                new Assert\GreaterThan(['value' => 0, 'message' => 'Le prix doit être supérieur à zéro.']),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
