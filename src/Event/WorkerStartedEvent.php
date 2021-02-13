<?php

namespace Oka\WorkerBundle\Event;

use Oka\WorkerBundle\WorkerInterface;

/**
 * Dispatched when a worker has been started.
 *
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
final class WorkerStartedEvent
{
    private $worker;

    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }

    public function getWorker(): WorkerInterface
    {
        return $this->worker;
    }
}
