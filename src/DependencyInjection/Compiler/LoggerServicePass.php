<?php

namespace Oka\WorkerBundle\DependencyInjection\Compiler;

use Oka\WorkerBundle\Command\RunWorkerCommand;
use Oka\WorkerBundle\EventListener\StopWorkerOnRestartSignalListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class LoggerServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loggerId = $container->getParameter('oka_worker.logger_id');

        if (false === $container->has($loggerId)) {
            throw new \InvalidArgumentException(sprintf('Invalid service "%s" given.', $loggerId));
        }

        if (true === $container->hasDefinition(StopWorkerOnRestartSignalListener::class)) {
            $container
              ->getDefinition(StopWorkerOnRestartSignalListener::class)
              ->addArgument(new Reference($loggerId));
        }

        $runWorkerCommandDefinition = $container->getDefinition(RunWorkerCommand::class);
        $runWorkerCommandDefinition->addArgument(new Reference($loggerId));
    }
}
