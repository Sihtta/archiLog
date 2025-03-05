<?php

// src/Application/Service/Pomodoro/PomodoroTimer.php

namespace App\Application\Service\Pomodoro;

use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use App\Application\Service\Pomodoro\Strategy\StandardPomodoroStrategy;

class PomodoroTimer
{
    private int $remainingTime;
    private bool $isRunning = false;
    private PomodoroStrategyInterface $strategy;

    public function __construct(int $remainingTime = 1500)
    {
        $this->strategy = new StandardPomodoroStrategy();
        $this->remainingTime = $remainingTime;
    }

    public function start(): void
    {
        $this->isRunning = true;
    }

    public function pause(): void
    {
        $this->isRunning = false;
    }

    public function reset(): void
    {
        $this->remainingTime = $this->strategy->getDuration();
        $this->isRunning = false;
    }

    public function setStrategy(PomodoroStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
        $this->remainingTime = $this->strategy->getDuration();
    }

    public function getRemainingTime(): int
    {
        return $this->remainingTime;
    }

    public function isTimerRunning(): bool
    {
        return $this->isRunning;
    }

    public function tick(): void
    {
        if ($this->isRunning && $this->remainingTime > 0) {
            $this->remainingTime--;
        }
    }
}
