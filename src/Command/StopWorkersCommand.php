<?php

namespace Oka\WorkerBundle\Command;

use Oka\WorkerBundle\Service\WorkerManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>        
 */
class StopWorkersCommand extends Command
{
    protected static $defaultName = 'oka:worker:stop-worker';

    private $workerManager;

    public function __construct(WorkerManager $workerManager)
    {
        parent::__construct();
        
        $this->workerManager = $workerManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('workerName', InputArgument::OPTIONAL, 'Name of the worker to stop', null)
            ])
            ->setDescription('Stops workers after their current loop')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command sends a signal to stop any <info>oka:worker:run-workers</info> processes that are running.

    <info>php %command.full_name%</info>

Each worker command will finish the loop they are currently processing
and then exit. Worker commands are *not* automatically restarted: that
should be handled by a process control system.
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        $input->getArgument('workerName') ? $this->workerManager->stopOne($input->getArgument('workerName')) : $this->workerManager->stopAll();

        $io->success('Signal successfully sent to stop any running workers.');

        return 0;
    }
}
