<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Votre nom est requis'
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Votre nom ne doit pas dépasser les {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false, 
                'help' => 'Votre photo de profil doit être un type : PNG, WEBP ou JPEG et ne doit pas dépasser les 1M ',
                'constraints' => [
                    new File([
                        'extensions' => ['png', 'jpeg', 'jpg', 'webp'],
                        'extensionsMessage' => "Votre fichier n'est pas une image acceptée",
                        'maxSize' => '1M',
                        'maxSizeMessage' => "La taille de l'image ne doit pas dépasser {{ limit }}"
                    ])

                ]
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une addresse email'
                    ]),
                    new Email([
                        'message' => "L'adresse email est invalide"
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false, // car plainPassword n'existe pas dans l'entité
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'help' => 'Votre mot de passe doit faire au moins 6 caractères',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "j'accepte les conditions générales d'utilisation",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez accepter les conditions générales d'utilisation",
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider mon inscription',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
