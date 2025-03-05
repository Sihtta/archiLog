<?php

namespace App\Application\Service\Pomodoro\Strategy;

class ShortPomodoroStrategy implements PomodoroStrategyInterface
{
    public function getDuration(): int
    {
        return 15 * 60;
    }
}
