<?php

namespace App\Form;

use App\Domain\Task\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le titre de la tâche
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control'],
            ])
            // Champ pour la description (optionnel)
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            // Champ pour la date limite (optionnelle)
            ->add('dueDate', DateTimeType::class, [
                'label' => 'Date limite',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            // Champ pour le statut de la tâche
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'À faire' => Task::STATUS_TODO,
                    'En cours' => Task::STATUS_IN_PROGRESS,
                    'Terminée' => Task::STATUS_DONE,
                ],
                'attr' => ['class' => 'form-control'],
            ])
            // Bouton de soumission du formulaire
            ->add('save', SubmitType::class, [
                'label' => 'Créer la tâche',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Associe le formulaire à l'entité Task
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}