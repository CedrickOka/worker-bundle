<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Simon Delicata <simon.delicata@free.fr>
 * @author Tobias Schultze <http://tobion.de>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkerOnMemoryLimitListener implements EventSubscriberInterface
{
    private $memoryLimit;
    private $logger;
    private $memoryResolver;

    public function __construct(int $memoryLimit, ?LoggerInterface $logger = null, ?callable $memoryResolver = null)
    {
        $this->memoryLimit = $memoryLimit;
        $this->logger = $logger;
        $this->memoryResolver = $memoryResolver ?: static function () {
            return memory_get_usage(true);
        };
    }

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        $memoryResolver = $this->memoryResolver;
        $usedMemory = $memoryResolver();

        if ($usedMemory > $this->memoryLimit) {
            $event->getWorker()->stop();

            if (null !== $this->logger) {
                $this->logger->info('Worker ({workerName}) stopped due to memory limit of {limit} bytes exceeded ({memory} bytes used)', [
                    'workerName' => $event->getWorker()::getName(),
                    'limit' => $this->memoryLimit,
                    'memory' => $usedMemory,
                ]);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}
