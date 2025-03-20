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
        $this->remainingTime = $this->strategy->getDuration();
        $this->isRunning = false;
        $this->requestStack = $requestStack;
    }

    public function getRemainingTime(): int
    {
        return $this->remainingTime;
    }

    public function toggleRunning(): void
    {
        $this->isRunning = !$this->isRunning;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }
}