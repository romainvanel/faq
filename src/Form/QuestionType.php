<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'required' => false,
                'label' => 'Quelle est votre question ?',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez écrire une question'
                    ]),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Votre question doit faire plus de {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Votre question ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('contenu', TextareaType::class, [
                'required' => false,
                'label' => 'Développez votre question si vous le souhaitez',
                'attr' => [
                    'rows' => 10
                ],
                'constraints' => [
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Ce champs ne doit pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['labelButton']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'labelButton' => 'Poser votre question'
        ]);
    }
}
