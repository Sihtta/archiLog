<?php

// src/Application/Service/PomodoroService.php

namespace App\Application\Service;

use App\Application\Service\Pomodoro\PomodoroTimer;
use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use App\Application\Service\Pomodoro\Strategy\ShortPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\StandardPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\LongPomodoroStrategy;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Domain\Task\Entity\Task;

class PomodoroService
{
    private PomodoroTimer $timer;
    private RequestStack $requestStack;
    private array $tasks = [];

    public function __construct(PomodoroTimer $timer, RequestStack $requestStack)
    {
        $this->timer = $timer;
        $this->requestStack = $requestStack;
        $this->loadTimerStateFromSession();

        // Assurer que le timer commence en pause par défaut.
        if (!$this->timer->isTimerRunning()) {
            $this->timer->pause();
        }
    }


    public function startTimer(): void
    {
        if (!$this->timer->isTimerRunning()) {
            $this->timer->start();
            $this->saveTimerStateToSession();
        }
    }

    public function pauseTimer(): void
    {
        $this->timer->pause();
        $this->saveTimerStateToSession();
    }

    public function resetTimer(): void
    {
        $this->timer->reset();
        $this->tasks = [];
        $this->saveTimerStateToSession();
    }

    public function getRemainingTime(): int
    {
        return $this->timer->getRemainingTime();
    }

    public function changeStrategy(PomodoroStrategyInterface $strategy): void
    {
        $this->timer->setStrategy($strategy);
        $this->saveStrategyToSession($strategy);
        $this->saveTimerStateToSession();
    }

    public function addTaskToSession(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getTimerState(): string
    {
        return $this->timer->isTimerRunning() ? 'Running' : 'Paused';
    }

    public function tick(): void
    {
        $this->timer->tick();
        $this->saveTimerStateToSession();
    }

    private function saveStrategyToSession(PomodoroStrategyInterface $strategy): void
    {
        $session = $this->requestStack->getSession();
        $session->set('pomodoro_strategy', get_class($strategy));
    }

    private function loadStrategyFromSession(): void
    {
        $session = $this->requestStack->getSession();
        $strategyClass = $session->get('pomodoro_strategy', StandardPomodoroStrategy::class);
        $this->changeStrategy(new $strategyClass());
    }

    private function saveTimerStateToSession(): void
    {
        $session = $this->requestStack->getSession();
        $session->set('remaining_time', $this->timer->getRemainingTime());
        $session->set('is_running', $this->timer->isTimerRunning());
    }

    private function loadTimerStateFromSession(): void
    {
        $session = $this->requestStack->getSession();
        $remainingTime = $session->get('remaining_time', 1500); // 25 minutes par défaut
        $isRunning = $session->get('is_running', false);

        // Si le timer n'est pas en marche (figé), il doit être en pause par défaut
        if (!$isRunning) {
            $this->timer = new PomodoroTimer($remainingTime);
            $this->timer->pause();  // Assurer qu'il est en pause
        } else {
            $this->timer = new PomodoroTimer($remainingTime);
            $this->timer->start();  // Si en marche, on le démarre
        }
    }
}
