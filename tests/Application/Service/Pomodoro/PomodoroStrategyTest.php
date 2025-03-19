<?php

namespace Tests\Application\Service\Pomodoro\Strategy;

use PHPUnit\Framework\TestCase;
use App\Application\Service\Pomodoro\Strategy\LongPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\ShortPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\StandardPomodoroStrategy;
use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;

class PomodoroStrategyTest extends TestCase
{
    public function testLongPomodoroStrategy()
    {
        $strategy = new LongPomodoroStrategy();
        $this->assertInstanceOf(PomodoroStrategyInterface::class, $strategy);
        $this->assertEquals(45 * 60, $strategy->getDuration());
    }

    public function testShortPomodoroStrategy()
    {
        $strategy = new ShortPomodoroStrategy();
        $this->assertInstanceOf(PomodoroStrategyInterface::class, $strategy);
        $this->assertEquals(15 * 60, $strategy->getDuration());
    }

    public function testStandardPomodoroStrategy()
    {
        $strategy = new StandardPomodoroStrategy();
        $this->assertInstanceOf(PomodoroStrategyInterface::class, $strategy);
        $this->assertEquals(25 * 60, $strategy->getDuration());
    }
}
