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
        // Mock du service TaskService
        $taskServiceMock = $this->createMock(TaskService::class);
        $taskServiceMock->expects($this->once())
            ->method('checkTaskDeadlines');

        // Instanciation de la commande avec le mock
        $command = new CheckTaskDeadlinesCommand($taskServiceMock);
        $commandTester = new CommandTester($command);

        // Exécution de la commande
        $commandTester->execute([]);

        // Vérification du message de sortie
        $this->assertStringContainsString('Notifications envoyées.', $commandTester->getDisplay());

        // Vérification du code de retour
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
