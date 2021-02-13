<?php

namespace Oka\WorkerBundle\Event;

use Oka\WorkerBundle\WorkerInterface;

/**
 * Dispatched after the worker processed a message or didn't receive a message at all.
 *
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
final class WorkerRunningEvent
{
    private $worker;
    private $isWorkerIdle;

    public function __construct(WorkerInterface $worker, bool $isWorkerIdle)
    {
        $this->worker = $worker;
        $this->isWorkerIdle = $isWorkerIdle;
    }

    public function getWorker(): WorkerInterface
    {
        return $this->worker;
    }

    /**
     * Returns true when no message has been received by the worker.
     */
    public function isWorkerIdle(): bool
    {
        return $this->isWorkerIdle;
    }
}
