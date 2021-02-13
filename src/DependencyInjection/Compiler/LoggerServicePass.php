<?php

namespace Oka\WorkerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Oka\WorkerBundle\EventListener\StopWorkerOnRestartSignalListener;
use Oka\WorkerBundle\Command\RunWorkerCommand;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class LoggerServicePass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (null === ($loggerId = $container->getParameter('oka_worker.logger_id'))) {
			return;
		}
		
		if (false === $container->hasDefinition($loggerId)) {
			return;
		}
		
		if (true === $container->hasDefinition(StopWorkerOnRestartSignalListener::class)) {
		    $container
		      ->getDefinition(StopWorkerOnRestartSignalListener::class)
		      ->replaceArgument(1, new Reference($loggerId));
		}
		
		$runWorkerCommandDefinition = $container->getDefinition(RunWorkerCommand::class);
		$runWorkerCommandDefinition->replaceArgument(2, new Reference($loggerId));
	}
}
