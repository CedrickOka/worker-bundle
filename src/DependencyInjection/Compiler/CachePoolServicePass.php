<?php

namespace Oka\WorkerBundle\DependencyInjection\Compiler;

use Oka\WorkerBundle\EventListener\DispatchRestartSignalListener;
use Oka\WorkerBundle\EventListener\StopWorkerOnRestartSignalListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class CachePoolServicePass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (null === ($cachePoolId = $container->getParameter('oka_worker.cache_pool_id'))) {
			return;
		}
		
		if (false === $container->hasDefinition($cachePoolId)) {
			return;
		}
		
		$dispatchRestartSignalListenerDefnition = $container->setDefinition(
		    DispatchRestartSignalListener::class, 
		    new Definition(DispatchRestartSignalListener::class, [new Reference($cachePoolId)])
	    );
		$dispatchRestartSignalListenerDefnition->addTag('kernel.event_subscriber');
		
		$stopWorkerOnRestartSignalListenerDefnition = $container->setDefinition(
		    StopWorkerOnRestartSignalListener::class,
		    new Definition(StopWorkerOnRestartSignalListener::class, [new Reference($cachePoolId)])
	    );
		$stopWorkerOnRestartSignalListenerDefnition->addTag('kernel.event_subscriber');
	}
}
