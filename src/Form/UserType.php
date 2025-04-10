<?php

namespace App\Form;

use App\Domain\User\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '50',
                ],
                'label' => 'Nom / Prénom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(), // Vérifie que le champ n'est pas vide
                    new Assert\Length(['min' => 2, 'max' => 50]) // Vérifie la longueur du texte
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '50',
                ],
                'required' => false, // Champ optionnel
                'label' => 'Pseudo (Facultatif)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]) // Vérifie la longueur si renseigné
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Mot de passe pour confirmer',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('maxTasksTodo', IntegerType::class, [
                'label' => 'Limite de tâches à faire',
                'required' => false, // Champ optionnel
            ])
            ->add('maxTasksInProgress', IntegerType::class, [
                'label' => 'Limite de tâches en cours',
                'required' => false, // Champ optionnel
            ])
            ->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Modifier mon profil'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Associe ce formulaire à l'entité User
        ]);
    }
}
