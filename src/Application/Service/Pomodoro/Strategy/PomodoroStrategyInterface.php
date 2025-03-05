<?php

namespace App\Application\Service\Pomodoro\Strategy;

interface PomodoroStrategyInterface
{
    public function getDuration(): int;
}
