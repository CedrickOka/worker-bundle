<?php

namespace Oka\WorkerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>        
 */
class StopWorkersCommand extends WorkerCommand
{
    protected static $defaultName = 'oka:worker:stop-worker';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();
        
        $this
            ->setDescription('Stops workers after their current loop')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command sends a signal to stop any <info>oka:worker:run-workers</info> processes that are running.

    <info>php %command.full_name%</info>

Use the --tags option to define tags list to pass to the worker during run:

    <info>php %command.full_name% <workerName> --tags=web --tags=mobile</info>

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

        $input->getArgument('workerName') ? 
            $this->workerManager->stopOne($input->getArgument('workerName'), $input->getOption('tags')) : 
            $this->workerManager->stopAll($input->getOption('tags'));

        $io->success('Signal successfully sent to stop any running workers.');

        return 0;
    }
}
