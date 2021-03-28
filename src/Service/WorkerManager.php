<?php

namespace Oka\WorkerBundle\Service;

use Oka\WorkerBundle\AbstractWorker;
use Oka\WorkerBundle\WorkerInterface;
use Oka\WorkerBundle\EventListener\StopWorkerOnRestartSignalListener;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class WorkerManager
{
    private $workerLocator;
    private $cachePool;

    public function __construct(ServiceLocator $workerLocator, CacheItemPoolInterface $cachePool = null)
    {
        $this->workerLocator = $workerLocator;
        $this->cachePool = $cachePool;
    }

    public function execute(string $workerName, array $options = [], array $tags = []): WorkerInterface
    {
        /** @var \Oka\WorkerBundle\WorkerInterface $worker */
        $worker = $this->workerLocator->get($workerName);
        
        if (!$worker instanceof AbstractWorker) {
            throw new \LogicException(sprintf('The worker "%s" class must be extend "%s" class for use this command.', get_class($worker), AbstractWorker::class));
        }
        
        $worker->setTags($tags);
        
        $worker->beforeRun($options);
        
        $worker->run($options);
        
        $worker->afterRun($options);
        
        return $worker;
    }
    
    public function stopAll(array $tags = []): void
    {
        if (null === $this->cachePool) {
            throw new \LogicException('Define "oka.worker.cache_pool_id" configuration value for use this feature.');
        }
        
        $cacheItem = $this->cachePool->getItem(StopWorkerOnRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY);
        $cacheItem->set($this->createRequestedTimestampItem($tags));
        
        $this->cachePool->save($cacheItem);
    }
    
    public function stopOne(string $workerName, array $tags = []): void
    {
        if (null === $this->cachePool) {
            throw new \LogicException('Define "oka.worker.cache_pool_id" configuration value for use this feature.');
        }
        
        $cacheItem = $this->cachePool->getItem(sprintf('%s.%s', StopWorkerOnRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY, $workerName));
        $cacheItem->set($this->createRequestedTimestampItem($tags));
        
        $this->cachePool->save($cacheItem);
    }
    
    private function createRequestedTimestampItem(array $tags = []): array
    {
        return [
            'issuedAt' => microtime(true),
            'tags' => $tags
        ];
    }
}
