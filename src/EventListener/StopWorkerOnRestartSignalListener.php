<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Oka\WorkerBundle\Event\WorkerStartedEvent;
use Oka\WorkerBundle\WorkerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Ryan Weaver <ryan@symfonycasts.com>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkerOnRestartSignalListener implements EventSubscriberInterface
{
    public const RESTART_REQUESTED_TIMESTAMP_KEY = 'oka_worker.workers.restart_requested_timestamp';

    private $cachePool;
    private $logger;
    private $workerStartedAt;

    public function __construct(CacheItemPoolInterface $cachePool, ?LoggerInterface $logger = null)
    {
        $this->cachePool = $cachePool;
        $this->logger = $logger;
    }

    public function onWorkerStarted(): void
    {
        $this->workerStartedAt = microtime(true);
    }

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        $worker = $event->getWorker();

        if (true === $this->shouldRestart($worker)) {
            $worker->stop();

            if (null !== $this->logger) {
                $this->logger->info(
                    'Worker "{name}" stopped because a restart was requested.',
                    [
                        'name' => $worker::getName(),
                        'id' => $worker->getId(),
                        'tags' => $worker->getTags(),
                    ]
                );
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerStartedEvent::class => 'onWorkerStarted',
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }

    private function shouldRestart(WorkerInterface $worker): bool
    {
        $cacheItem = $this->cachePool->getItem(self::RESTART_REQUESTED_TIMESTAMP_KEY);

        if (false === $cacheItem->isHit()) {
            // no restart has ever been scheduled
            return false;
        }

        $restartRequested = $cacheItem->get();

        if ($this->workerStartedAt > $restartRequested['issuedAt']) {
            return false;
        }
        if (isset($restartRequested['workerName']) && $worker::getName() !== $restartRequested['workerName']) {
            return false;
        }

        if (empty($restartRequested['criteria'])) {
            return true;
        }
        if (isset($restartRequested['criteria']['id']) && $worker->getId() === $restartRequested['criteria']['id']) {
            return true;
        }
        if (isset($restartRequested['criteria']['tags']) && empty($restartRequested['criteria']['tags'])) {
            return true;
        }

        $diff = array_diff($restartRequested['criteria']['tags'], $worker->getTags());

        return count($diff) !== count($restartRequested['criteria']['tags']);
    }
}
