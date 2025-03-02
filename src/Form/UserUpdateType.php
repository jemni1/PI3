<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // CIN field
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'CIN',
                    'maxlength' => '8',
                    'minlength' => '8',
                    'inputmode' => 'numeric',
                ],
            ])
            // Role field as ChoiceType (select dropdown)
            ->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'choices' => [
                    'Agriculture' => 'ROLE_AGRICULTURE',
                    'Worker' => 'ROLE_WORKER',
                    'Client' => 'ROLE_CLIENT',
                ],
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            
            // Submit button
            ->add('submit', SubmitType::class, [
                'label' => 'Update Profile',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
            
        // Add data transformer for roles field
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                // Transform array to string (for the form display)
                function ($rolesArray) {
                    // Return the first role (or a default)
                    return $rolesArray[0] ?? 'ROLE_CLIENT';
                },
                // Transform string back to array (for the entity)
                function ($rolesString) {
                    // Return as array
                    return [$rolesString];
                }
            ));
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}