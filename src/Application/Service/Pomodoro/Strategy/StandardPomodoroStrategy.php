<?php

namespace App\Application\Service\Pomodoro\Strategy;

class StandardPomodoroStrategy implements PomodoroStrategyInterface
{
    public function getDuration(): int
    {
        return 25 * 60;
    }
}
