<?php

namespace Oka\WorkerBundle\Event;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
final class StopWorkerEvent
{
    private $workerName;

    public function __construct(string $workerName = null)
    {
        $this->workerName = $workerName;
    }

    public function getWorkerName():? string
    {
        return $this->workerName;
    }
}
