<?php

namespace Tests\Application\Service;

use App\Application\Service\PomodoroService;
use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class PomodoroServiceTest extends TestCase
{
    public function testGetRemainingTimeReturnsInitialDuration()
    {
        $strategy = $this->createMock(PomodoroStrategyInterface::class);
        $strategy->method('getDuration')->willReturn(25 * 60);
        
        $requestStack = $this->createMock(RequestStack::class);
        
        $pomodoroService = new PomodoroService($strategy, $requestStack);
        
        $this->assertEquals(25 * 60, $pomodoroService->getRemainingTime());
    }

    public function testToggleRunningChangesState()
    {
        $strategy = $this->createMock(PomodoroStrategyInterface::class);
        $strategy->method('getDuration')->willReturn(25 * 60);
        
        $requestStack = $this->createMock(RequestStack::class);
        
        $pomodoroService = new PomodoroService($strategy, $requestStack);
        
        $this->assertFalse($pomodoroService->isRunning());
        
        $pomodoroService->toggleRunning();
        $this->assertTrue($pomodoroService->isRunning());
        
        $pomodoroService->toggleRunning();
        $this->assertFalse($pomodoroService->isRunning());
    }
}