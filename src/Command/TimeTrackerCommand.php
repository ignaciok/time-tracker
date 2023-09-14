<?php

namespace App\Command;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class TimeTrackerCommand extends Command
{
    protected static $defaultName = 'app:time-tracker';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('CLI for Time Tracker')
            ->addArgument('action', InputArgument::REQUIRED, 'The action to perform (start/end/list)')
            ->addArgument('taskName', InputArgument::OPTIONAL, 'The name of the task');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $action = $input->getArgument('action');
        $taskName = $input->getArgument('taskName');

        switch ($action) {
            case 'start':
                $this->startTask($taskName, $output);
                break;

            case 'end':
                $this->endTask($taskName, $output);
                break;

            case 'list':
                $this->listTasks($output);
                break;

            default:
                $output->writeln('<error>Invalid action</error>');
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function startTask(string $taskName, OutputInterface $output)
    {
        $task = new Task();
        $task->setName($taskName);
        $task->setStatus('started');
        $task->setStartTime(new \DateTime());

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $output->writeln('<info>Task started.</info>');
    }

    private function endTask(string $taskName, OutputInterface $output)
    {
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['name' => $taskName, 'status' => 'started']);

        if (!$task) {
            $output->writeln('<error>Task not found or already ended.</error>');
            return;
        }

        $task->setStatus('ended');
        $task->setEndTime(new \DateTime());

		$interval = $task->getStartTime()->diff($task->getEndTime());
		$elapsedTimeInSeconds = $interval->h * 3600 + $interval->i * 60 + $interval->s;
		$task->setTime($elapsedTimeInSeconds);
		
        $this->entityManager->flush();

        $output->writeln('<info>Task ended.</info>');
    }

    private function listTasks(OutputInterface $output)
    {
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $tasks = $taskRepository->findAll();

        if (empty($tasks)) {
            $output->writeln('<info>No tasks found.</info>');
            return;
        }

        foreach ($tasks as $task) {
            $output->writeln(sprintf(
                '<info>Task: %s, Status: %s, Start Time: %s, End Time: %s, Total Time: %s</info>',
                $task->getName(),
                $task->getStatus(),
                $task->getStartTime()->format('Y-m-d H:i:s'),
                $task->getEndTime() ? $task->getEndTime()->format('Y-m-d H:i:s') : 'N/A',
                $task->getTime() ?? 'N/A'
            ));
        }
    }
}
