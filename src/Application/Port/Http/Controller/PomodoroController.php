<?php

// src/Application/Port/Http/Controller/PomodoroController.php

namespace App\Application\Port\Http\Controller;

use App\Application\Service\PomodoroService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\Service\Pomodoro\Strategy\ShortPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\StandardPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\LongPomodoroStrategy;

class PomodoroController extends AbstractController
{
    private PomodoroService $pomodoroService;

    public function __construct(PomodoroService $pomodoroService)
    {
        $this->pomodoroService = $pomodoroService;
    }

    /**
     * @Route("/pomodoro", name="pomodoro_index")
     */
    public function index()
    {
        $remainingTime = $this->pomodoroService->getRemainingTime();
        $isRunning = $this->pomodoroService->getTimerState() === 'Running';

        return $this->render('pages/pomodoro/index.html.twig', [
            'remainingTime' => $remainingTime,
            'isRunning' => $isRunning
        ]);
    }

    /**
     * @Route("/pomodoro/start", name="pomodoro_start")
     */
    public function start()
    {
        $this->pomodoroService->startTimer();
        return $this->redirectToRoute('pomodoro_index');
    }

    /**
     * @Route("/pomodoro/pause", name="pomodoro_pause")
     */
    public function pause()
    {
        $this->pomodoroService->pauseTimer();
        return $this->redirectToRoute('pomodoro_index');
    }

    /**
     * @Route("/pomodoro/reset", name="pomodoro_reset")
     */
    public function reset()
    {
        $this->pomodoroService->resetTimer();
        return $this->redirectToRoute('pomodoro_index');
    }

    /**
     * @Route("/pomodoro/strategy/{duration}", name="pomodoro_strategy")
     */
    public function setStrategy(string $duration)
    {
        switch ($duration) {
            case 'short':
                $this->pomodoroService->changeStrategy(new ShortPomodoroStrategy());
                break;
            case 'standard':
                $this->pomodoroService->changeStrategy(new StandardPomodoroStrategy());
                break;
            case 'long':
                $this->pomodoroService->changeStrategy(new LongPomodoroStrategy());
                break;
            default:
                return $this->redirectToRoute('pomodoro_index');
        }

        return $this->redirectToRoute('pomodoro_index');
    }
}
