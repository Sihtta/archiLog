<?php

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
        $this->remainingTime = $this->strategy->getDuration(); // Initialise le temps restant avec la durée de la stratégie par défaut
        $this->isRunning = false;
        $this->requestStack = $requestStack;
    }

    /**
     * Retourne le temps restant pour la session Pomodoro.
     */
    public function getRemainingTime(): int
    {
        return $this->remainingTime;
    }

    /**
     * Démarre ou met en pause la session Pomodoro.
     */
    public function toggleRunning(): void
    {
        $this->isRunning = !$this->isRunning;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }
}