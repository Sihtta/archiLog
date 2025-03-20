<?php

namespace Tests\Application\Service\Pomodoro;

use PHPUnit\Framework\TestCase;
use App\Application\Service\Pomodoro\PomodoroTimer;
use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use App\Application\Service\Pomodoro\Strategy\StandardPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\LongPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\ShortPomodoroStrategy;

class PomodoroTimerTest extends TestCase
{
    public function testDefaultInitialization()
    {
        $timer = new PomodoroTimer();
        $this->assertInstanceOf(PomodoroTimer::class, $timer);

        $strategy = new StandardPomodoroStrategy();
        $this->assertEquals($strategy->getDuration(), $this->getRemainingTime($timer));
    }

    public function testCustomInitialization()
    {
        $timer = new PomodoroTimer(1800);
        $this->assertEquals(1800, $this->getRemainingTime($timer));
    }

    public function testSetStrategy()
    {
        $timer = new PomodoroTimer();
        
        $longStrategy = new LongPomodoroStrategy();
        $timer->setStrategy($longStrategy);
        $this->assertEquals($longStrategy->getDuration(), $this->getRemainingTime($timer));

        $shortStrategy = new ShortPomodoroStrategy();
        $timer->setStrategy($shortStrategy);
        $this->assertEquals($shortStrategy->getDuration(), $this->getRemainingTime($timer));
    }

    /**
     * Accède à la propriété privée `remainingTime` en utilisant Reflection.
     */
    private function getRemainingTime(PomodoroTimer $timer): int
    {
        $reflection = new \ReflectionClass($timer);
        $property = $reflection->getProperty('remainingTime');
        $property->setAccessible(true);
        return $property->getValue($timer);
    }
}
