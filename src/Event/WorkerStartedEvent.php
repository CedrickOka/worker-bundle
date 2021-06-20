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
    private $options;

    public function __construct(WorkerInterface $worker, array $options = [])
    {
        $this->worker = $worker;
        $this->options = $options;
    }

    public function getWorker(): WorkerInterface
    {
        return $this->worker;
    }
    
    public function getOptions(): array
    {
        return $this->options;
    }
}
