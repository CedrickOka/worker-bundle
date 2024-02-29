<?php

namespace Oka\WorkerBundle\Event;

use Oka\WorkerBundle\WorkerInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class WorkerEvent
{
    protected $worker;

    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }

    public function getWorker(): WorkerInterface
    {
        return $this->worker;
    }
}
