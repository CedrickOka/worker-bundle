<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Oka\WorkerBundle\Event\WorkerStartedEvent;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Oka\WorkerBundle\WorkerInterface;

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

    public function __construct(CacheItemPoolInterface $cachePool, LoggerInterface $logger = null)
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
        if (true === $this->shouldRestart() || true === $this->shouldRestart($event->getWorker())) {
            $event->getWorker()->stop();

            if (null !== $this->logger) {
                $this->logger->info('Worker stopped because a restart was requested.');
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

    private function shouldRestart(WorkerInterface $worker = null): bool
    {
        $cacheItem = $this->cachePool->getItem(
            null === $worker ?
            self::RESTART_REQUESTED_TIMESTAMP_KEY :
            sprintf('%s.%s', self::RESTART_REQUESTED_TIMESTAMP_KEY, $worker::getName())
        );
        
        if (false === $cacheItem->isHit()) {
            // no restart has ever been scheduled
            return false;
        }

        $restartRequested = $cacheItem->get();
        
        if ($this->workerStartedAt > $restartRequested['issuedAt']) {
            return false;
        }

        if (null === $worker || true === empty($restartRequested['tags'])) {
            return true;
        }

        $diff = array_diff($restartRequested['tags'], $worker->getTags());

        return count($diff) !== count($restartRequested['tags']);
    }
}
