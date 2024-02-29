<?php

namespace Oka\WorkerBundle\DependencyInjection\Compiler;

use Oka\WorkerBundle\EventListener\StopWorkerOnRestartSignalListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class CachePoolServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $cachePoolId = $container->getParameter('oka_worker.cache_pool_id');

        if (false === $container->has($cachePoolId)) {
            throw new \InvalidArgumentException(sprintf('Invalid service "%s" given.', $cachePoolId));
        }

        $workerManagerDefnition = $container->getDefinition('oka_worker.worker_manager');
        $workerManagerDefnition->addArgument(new Reference($cachePoolId));

        $stopWorkerOnRestartSignalListenerDefnition = $container->setDefinition(
            StopWorkerOnRestartSignalListener::class,
            new Definition(StopWorkerOnRestartSignalListener::class, [new Reference($cachePoolId)])
        );
        $stopWorkerOnRestartSignalListenerDefnition->addTag('kernel.event_subscriber');
    }
}
