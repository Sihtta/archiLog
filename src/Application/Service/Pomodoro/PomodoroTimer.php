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

    public function setStrategy(PomodoroStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
        $this->remainingTime = $this->strategy->getDuration();
    }
}
