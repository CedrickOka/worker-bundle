<?php

namespace Oka\WorkerBundle\Command;

use Oka\WorkerBundle\EventListener\StopWorkerOnLoopLimitListener;
use Oka\WorkerBundle\EventListener\StopWorkerOnMemoryLimitListener;
use Oka\WorkerBundle\EventListener\StopWorkerOnTimeLimitListener;
use Oka\WorkerBundle\Service\WorkerManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class RunWorkerCommand extends WorkerCommand
{
    protected static $defaultName = 'oka:worker:run-worker';
    
    private $eventDispatcher;
    private $logger;

    public function __construct(WorkerManager $workerManager, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger = null)
    {
        parent::__construct($workerManager);
        
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();
        
        $this
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit the number of processed loops')
            ->addOption('memory-limit', 'm', InputOption::VALUE_REQUIRED, 'The memory limit the worker can consume')
            ->addOption('time-limit', 't', InputOption::VALUE_REQUIRED, 'The time limit in seconds the worker can run')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Seconds to sleep before asking for new task after no messages were found', 1)
            ->addOption('extras', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Extra options to pass to the worker during run')
            ->setDescription('Runs worker')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command runs worker.

    <info>php %command.full_name% <workerName></info>

Use the --limit option to limit the number of loops processed:

    <info>php %command.full_name% <workerName> --limit=10</info>

Use the --memory-limit option to stop the worker if it exceeds a given memory usage limit. You can use shorthand byte values [K, M or G]:

    <info>php %command.full_name% <workerName> --memory-limit=128M</info>

Use the --time-limit option to stop the worker when the given time limit (in seconds) is reached:

    <info>php %command.full_name% <workerName> --time-limit=3600</info>

Use the --extras option to define options to pass to the worker during run:

    <info>php %command.full_name% <workerName> --extras=name=value</info>

Use the --tags option to define tags list to pass to the worker during run:

    <info>php %command.full_name% <workerName> --tags=web --tags=mobile</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        if (!$input->getArgument('workerName')) {
            $io->block('Which worker do you want to run?', null, 'fg=white;bg=blue', ' ', true);
            $io->writeln('Enter which worker you want to run.');
            
            $question = new Question('Enter worker name to run:');
            $input->setArgument('workerName', $io->askQuestion($question));
        }

        if (!$input->getArgument('workerName')) {
            throw new RuntimeException('Please pass the worker name.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopsWhen = [];
        
        if ($limit = $input->getOption('limit')) {
            $stopsWhen[] = "processed {$limit} loops";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnLoopLimitListener($limit, $this->logger));
        }

        if ($memoryLimit = $input->getOption('memory-limit')) {
            $stopsWhen[] = "exceeded {$memoryLimit} of memory";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnMemoryLimitListener($this->convertToBytes($memoryLimit), $this->logger));
        }

        if ($timeLimit = $input->getOption('time-limit')) {
            $stopsWhen[] = "been running for {$timeLimit}s";
            $this->eventDispatcher->addSubscriber(new StopWorkerOnTimeLimitListener($timeLimit, $this->logger));
        }

        $stopsWhen[] = 'received a stop signal via the oka:worker:stop-workers command';

        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);
        $io->success(sprintf('Running "%s" worker.', $input->getArgument('workerName')));

        if ($stopsWhen) {
            $last = array_pop($stopsWhen);
            $stopsWhen = ($stopsWhen ? implode(', ', $stopsWhen).' or ' : '').$last;
            $io->comment("The worker will automatically exit once it has {$stopsWhen}.");
        }

        $io->comment('Quit the worker with CONTROL-C.');

        if (OutputInterface::VERBOSITY_VERBOSE > $output->getVerbosity()) {
            $io->comment('Re-run the command with a -vv option to see logs about consumed messages.');
        }

        $options = [
            'sleep' => $input->getOption('sleep') * 1000000
        ];

        if (false === empty($input->getOption('extras'))) {
            foreach ($input->getOption('extras') as $value) {
                $option = explode('=', $value);
                
                if (true === isset($option[0])) {
                    $options[$option[0]] = $option[1] ?? true;
                }
            }
        }
        
        $this->workerManager->execute($input->getArgument('workerName'), $options, $input->getOption('tags'));

        return 0;
    }

    private function convertToBytes(string $memoryLimit): int
    {
        $memoryLimit = strtolower($memoryLimit);
        $max = ltrim($memoryLimit, '+');
        
        if (0 === strpos($max, '0x')) {
            $max = \intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = \intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr(rtrim($memoryLimit, 'b'), -1)) {
            case 't': $max *= 1024;
            // no break
            case 'g': $max *= 1024;
            // no break
            case 'm': $max *= 1024;
            // no break
            case 'k': $max *= 1024;
        }

        return $max;
    }
}
