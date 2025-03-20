<?php

namespace Tests\Application\Command;

use App\Application\Command\CheckTaskDeadlinesCommand;
use App\Application\Service\TaskService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;

class CheckTaskDeadlinesCommandTest extends TestCase
{
    public function testExecute()
    {
        $taskServiceMock = $this->createMock(TaskService::class);
        $taskServiceMock->expects($this->once())
            ->method('checkTaskDeadlines');

        $command = new CheckTaskDeadlinesCommand($taskServiceMock);
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $this->assertStringContainsString('Notifications envoyées.', $commandTester->getDisplay());

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
