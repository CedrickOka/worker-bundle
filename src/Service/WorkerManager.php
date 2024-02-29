<?php

namespace Oka\WorkerBundle\Service;

use Oka\WorkerBundle\AbstractWorker;
use Oka\WorkerBundle\EventListener\StopWorkerOnRestartSignalListener;
use Oka\WorkerBundle\WorkerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class WorkerManager
{
    private $workerLocator;
    private $cachePool;

    public function __construct(ServiceLocator $workerLocator, ?CacheItemPoolInterface $cachePool = null)
    {
        $this->workerLocator = $workerLocator;
        $this->cachePool = $cachePool;
    }

    public function start(string $workerName, array $tags = [], array $attributes = [], int $loopDelay = 0): WorkerInterface
    {
        /** @var AbstractWorker $worker */
        $worker = $this->workerLocator->get($workerName);

        if (!$worker instanceof AbstractWorker) {
            throw new \LogicException(sprintf('The worker "%s" class must be extend "%s" class for use this method.', get_class($worker), AbstractWorker::class));
        }

        $worker->setId(Uuid::uuid4());
        $worker->setTags($tags);
        $worker->setAttributes($attributes);
        $worker->setLoopDelay($loopDelay);

        $worker->beforeRun();
        $worker->run();
        $worker->afterRun();

        return $worker;
    }

    public function stop(?string $workerName = null, array $criteria = []): void
    {
        if (null === $this->cachePool) {
            throw new \LogicException('Define "oka.worker.cache_pool_id" configuration value for use this feature.');
        }

        $item = [
            'issuedAt' => microtime(true),
            'criteria' => $criteria,
        ];

        if (null !== $workerName) {
            $item['workerName'] = $workerName;
        }

        $cacheItem = $this->cachePool->getItem(StopWorkerOnRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY);
        $cacheItem->set($item);

        $this->cachePool->save($cacheItem);
    }
}
