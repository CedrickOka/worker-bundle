<?php

namespace Oka\WorkerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkersCommand extends WorkerCommand
{
    protected static $defaultName = 'oka:worker:stop-worker';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'The identifier of the worker', null)
            ->setDescription('Stops workers after their current loop')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command sends a signal to stop any <info>oka:worker:run-workers</info> processes that are running.

    <info>php %command.full_name%</info>

Use the --tags option to define tags list to pass to the worker during run:

    <info>php %command.full_name% <workerName> --id=0eb77bc8-6fb5-11ed-8568-0242ac120009 --tags=web --tags=mobile</info>

Each worker command will finish the loop they are currently processing
and then exit. Worker commands are *not* automatically restarted: that
should be handled by a process control system.
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        $criteria = [];

        if ($input->hasOption('id')) {
            $criteria['id'] = $input->getOption('id');
        }
        if ($input->hasOption('tags')) {
            $criteria['tags'] = $input->getOption('tags');
        }

        $this->workerManager->stop($input->getArgument('workerName'), $criteria);

        $io->success('Signal successfully sent to stop any running workers.');

        return 0;
    }
}
