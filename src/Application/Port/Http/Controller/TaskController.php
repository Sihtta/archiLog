<?php

namespace App\Application\Port\Http\Controller;

use App\Application\Service\TaskService;
use App\Domain\Task\Entity\Task;
use App\Form\TaskFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/tasks', name: 'task_index')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        $user = $this->getUser();
        $tasks = $this->taskService->getTasksByUser($user);

        return $this->render('pages/task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/tasks/new', name: 'task_create')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour créer une tâche.');
        }

        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->createTask(
                $task->getTitle(),
                $task->getDescription(),
                $task->getDueDate(),
                $user
            );
            $this->addFlash('success', 'Tâche créée avec succès !');

            return $this->redirectToRoute('task_index');
        }

        return $this->render('pages/task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->updateTask($task);
            $this->addFlash('success', 'Tâche mise à jour avec succès.');

            return $this->redirectToRoute('task_index');
        }

        return $this->render('pages/task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function delete(Task $task): Response
    {
        $this->taskService->deleteTask($task);
        $this->addFlash('success', 'Tâche supprimée.');

        return $this->redirectToRoute('task_index');
    }

    #[Route('/tasks/{id}/move/{status}', name: 'task_move')]
    public function moveTask(Task $task, string $status): Response
    {
        if (!in_array($status, [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS, Task::STATUS_DONE])) {
            throw $this->createNotFoundException("Statut invalide");
        }

        $this->taskService->updateTaskStatus($task, $status);
        $this->addFlash('success', 'Statut de la tâche mis à jour.');

        return $this->redirectToRoute('task_index');
    }
}
