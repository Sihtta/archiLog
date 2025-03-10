<?php

namespace App\Application\Command;

use App\Application\Service\TaskService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:check-task-deadlines')]
class CheckTaskDeadlinesCommand extends Command
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        parent::__construct();
        $this->taskService = $taskService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->taskService->checkTaskDeadlines();
        $output->writeln('Notifications envoyées.');
        return Command::SUCCESS;
    }
}
