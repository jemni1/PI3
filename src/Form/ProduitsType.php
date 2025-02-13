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
        
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom du produit',
        
        ])
        ->add('prix', IntegerType::class, [
            'label' => 'Prix',
       
        ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false, // Important : Ne pas lier directement à l'entité
                'required' => false,
              
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
