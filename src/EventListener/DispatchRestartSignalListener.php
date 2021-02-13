<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\StopWorkerEvent;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Tobias Schultze <http://tobion.de>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class DispatchRestartSignalListener implements EventSubscriberInterface
{
    public const RESTART_REQUESTED_TIMESTAMP_KEY = 'oka_worker.workers.restart_requested_timestamp';
    
    private $cachePool;
    
    public function __construct(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }
    
    public function onStopWorker(StopWorkerEvent $event): void
    {
        $cacheItem = $this->cachePool->getItem(
            null === $event->getWorkerName() ? 
            self::RESTART_REQUESTED_TIMESTAMP_KEY : 
            sprintf('%s.%s', self::RESTART_REQUESTED_TIMESTAMP_KEY, $event->getWorkerName())
        );
        $cacheItem->set(microtime(true));
        
        $this->cachePool->save($cacheItem);
    }

    public static function getSubscribedEvents()
    {
        return [
            StopWorkerEvent::class => ['onStopWorker', 100],
        ];
    }
}
