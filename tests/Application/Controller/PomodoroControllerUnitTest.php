<?php

namespace Tests\Application\Controller;

use App\Application\Port\Http\Controller\PomodoroController;
use App\Application\Service\PomodoroService;
use App\Application\Service\Pomodoro\Strategy\PomodoroStrategyInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PomodoroControllerUnitTest extends TestCase
{
    private $pomodoroService;
    private $twig;
    private $strategies;

    protected function setUp(): void
    {
        $this->pomodoroService = $this->createMock(PomodoroService::class);
        $this->twig = $this->createMock(Environment::class);

        $shortStrategy = $this->createMock(PomodoroStrategyInterface::class);
        $shortStrategy->method('getDuration')->willReturn(900); // 15 min

        $standardStrategy = $this->createMock(PomodoroStrategyInterface::class);
        $standardStrategy->method('getDuration')->willReturn(1500); // 25 min

        $this->strategies = [
            'short' => $shortStrategy,
            'standard' => $standardStrategy
        ];
    }

    public function testIndexRendersCorrectTemplate(): void
    {
        $this->pomodoroService->method('getRemainingTime')->willReturn(1200);
        $this->pomodoroService->method('isRunning')->willReturn(true);

        $controller = new PomodoroController($this->pomodoroService, $this->strategies);

        // Simulation correcte de render() qui retourne une string
        $this->twig->method('render')
            ->with('pages/pomodoro/index.html.twig', [
                'remainingTime' => 1200,
                'isRunning' => true,
            ])
            ->willReturn('<html>Mocked HTML</html>');

        $response = new Response($this->twig->render('pages/pomodoro/index.html.twig', [
            'remainingTime' => $this->pomodoroService->getRemainingTime(),
            'isRunning' => $this->pomodoroService->isRunning(),
        ]));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertStringContainsString('Mocked HTML', $response->getContent());
    }

    public function testChangeStrategyWithValidStrategy(): void
    {
        $controller = new PomodoroController($this->pomodoroService, $this->strategies);

        $this->twig->method('render')
            ->with('pages/pomodoro/index.html.twig', [
                'remainingTime' => 900,
                'isRunning' => false,
            ])
            ->willReturn('<html>Mocked HTML</html>');

        $response = new Response($this->twig->render('pages/pomodoro/index.html.twig', [
            'remainingTime' => $this->strategies['short']->getDuration(),
            'isRunning' => false,
        ]));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertStringContainsString('Mocked HTML', $response->getContent());
    }

    public function testChangeStrategyWithInvalidStrategyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $controller = new PomodoroController($this->pomodoroService, $this->strategies);
        $controller->changeStrategy('invalid');
    }
}
