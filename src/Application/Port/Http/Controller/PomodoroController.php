<?php

// src/Application/Port/Http/Controller/PomodoroController.php

namespace App\Application\Port\Http\Controller;

use App\Application\Service\PomodoroService;
use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PomodoroController extends AbstractController
{
    private $pomodoroService;
    private $strategies;

    // Injection des stratégies Pomodoro et du service
    public function __construct(PomodoroService $pomodoroService, array $strategies)
    {
        $this->pomodoroService = $pomodoroService;
        $this->strategies = $strategies;
    }

    // Route pour afficher le timer
    #[Route('/pomodoro', name: 'pomodoro_index')]
    public function index(): Response
    {
        $remainingTime = $this->pomodoroService->getRemainingTime();
        $isRunning = $this->pomodoroService->isRunning();

        return $this->render('pages/pomodoro/index.html.twig', [
            'remainingTime' => $remainingTime,
            'isRunning' => $isRunning,
        ]);
    }

    /**
     * @Route("/pomodoro/strategy/{duration}", name="pomodoro_strategy")
     */
    public function changeStrategy(string $duration): Response
    {
        // Vérifie si la stratégie existe
        if (!isset($this->strategies[$duration])) {
            throw new \InvalidArgumentException('Invalid strategy name.');
        }

        $strategy = $this->strategies[$duration];
        $durationInSeconds = $strategy->getDuration();

        // Met à jour le timer avec la nouvelle durée
        return $this->render('pages/pomodoro/index.html.twig', [
            'remainingTime' => $durationInSeconds,
            'isRunning' => false, // Timer est en pause par défaut
        ]);
    }
}
