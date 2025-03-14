<?php

// src/Application/Service/Pomodoro/PomodoroService.php

namespace App\Application\Service;

use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PomodoroService
{
    private $strategy;
    private $remainingTime;

    public function __construct(PomodoroStrategyInterface $defaultStrategy, RequestStack $requestStack)
    {
        $this->strategy = $defaultStrategy;
        $this->remainingTime = $this->strategy->getDuration();
    }
}
