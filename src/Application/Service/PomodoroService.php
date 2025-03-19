<?php

// src/Application/Service/Pomodoro/PomodoroService.php

namespace App\Application\Service;

use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PomodoroService
{
    private $strategy;
    private $remainingTime;
    private $isRunning;
    private $requestStack;

    public function __construct(PomodoroStrategyInterface $defaultStrategy, RequestStack $requestStack)
    {
        $this->strategy = $defaultStrategy;
        $this->remainingTime = $this->strategy->getDuration();
        $this->isRunning = false;
        $this->requestStack = $requestStack;
    }

    // Obtient le temps restant
    public function getRemainingTime(): int
    {
        return $this->remainingTime;
    }

    // Démarre ou met en pause le timer
    public function toggleRunning(): void
    {
        $this->isRunning = !$this->isRunning;
    }

    // Retourne si le timer est en cours d'exécution
    public function isRunning(): bool
    {
        return $this->isRunning;
    }
}