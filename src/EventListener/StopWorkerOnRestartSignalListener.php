<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Oka\WorkerBundle\Event\WorkerStartedEvent;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Ryan Weaver <ryan@symfonycasts.com>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkerOnRestartSignalListener implements EventSubscriberInterface
{
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
        if (true === $this->shouldRestart() || true === $this->shouldRestart($event->getWorker()::getName())) {
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

    private function shouldRestart(string $workerName = null): bool
    {
        $cacheItem = $this->cachePool->getItem(
            null === $workerName ?
            DispatchRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY :
            sprintf('%s.%s', DispatchRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY, $workerName)
        );
        
        if (false === $cacheItem->isHit()) {
            // no restart has ever been scheduled
            return false;
        }

        return $this->workerStartedAt < $cacheItem->get();
    }
}
