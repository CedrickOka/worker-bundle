<?php

namespace Oka\WorkerBundle\Event;

use Oka\WorkerBundle\WorkerInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
final class WorkerRunningEvent extends WorkerEvent
{
    private $isWorkerIdle;

    public function __construct(WorkerInterface $worker, bool $isWorkerIdle)
    {
        parent::__construct($worker);

        $this->isWorkerIdle = $isWorkerIdle;
    }

    /**
     * Returns true when no task has been processed by the worker.
     */
    public function isWorkerIdle(): bool
    {
        return $this->isWorkerIdle;
    }
}
