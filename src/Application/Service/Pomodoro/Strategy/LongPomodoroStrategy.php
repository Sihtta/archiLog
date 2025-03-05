<?php

namespace App\Application\Service\Pomodoro\Strategy;

class LongPomodoroStrategy implements PomodoroStrategyInterface
{
    public function getDuration(): int
    {
        return 45 * 60;
    }
}
