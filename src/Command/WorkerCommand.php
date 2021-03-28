<?php

namespace Oka\WorkerBundle\Command;

use Oka\WorkerBundle\Service\WorkerManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
abstract class WorkerCommand extends Command
{
    protected $workerManager;
    
    public function __construct(WorkerManager $workerManager)
    {
        parent::__construct();
        
        $this->workerManager = $workerManager;
    }

    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('workerName', InputArgument::OPTIONAL, 'Name of the worker to stop', null),
                new InputOption('tags', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'The tags list to pass at the worker', [])
            ]);
    }
}
