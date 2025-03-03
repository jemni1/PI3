<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'minlength' => 2,
                    'placeholder' => 'Name',
                    'required' => true
                ]
            ])
            ->add('surname', TextType::class, [
                'label' => 'Surname',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'minlength' => 2,
                    'placeholder' => 'Surname',
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Username',
                    'minlength' => 3,
                    'pattern' => '^(?=.*\d)[a-zA-Z0-9_-]+$',
                    'title' => '3+ characters, include a number'
                ]
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'CIN',
                    'pattern' => '\d{8}',
                    'minlength' => 8,
                    'maxlength' => 8,
                    'title' => '8 digits required',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Email',
                    'required' => true
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'choices' => [
                    'Agriculture' => 'ROLE_AGRICULTURE',
                    'Worker' => 'ROLE_WORKER',
                    'Client' => 'ROLE_CLIENT',
                ],
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control form-control-lg',
                ],
            ])
            ->add('profilePictureFile', FileType::class, [
                'label' => 'Profile Picture',
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file',
                    'accept' => 'image/jpeg, image/png'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'form-control form-control-lg',
                        'placeholder' => 'Password',
                        'minlength' => 8,
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'attr' => [
                        'class' => 'form-control form-control-lg',
                        'placeholder' => 'Confirm Password',
                        'required' => true
                    ]
                ],
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('isMfaEnabled', CheckboxType::class, [
                'label' => 'Enable Two-Factor Authentication (MFA)',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to the terms and conditions',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);

        // Transformers for CIN and roles
        $builder->get('cin')->addModelTransformer(new CallbackTransformer(
            fn ($cinFromModel) => $cinFromModel,
            fn ($cinFromView) => $cinFromView && preg_match('/^\d{8}$/', trim($cinFromView)) ? trim($cinFromView) : null
        ));

        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            fn ($rolesFromModel) => $rolesFromModel[0] ?? null,
            fn ($roleFromView) => [$roleFromView]
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration'],
        ]);
        $resolver->setAllowedTypes('validation_groups', ['array', 'string']);
    }
}
