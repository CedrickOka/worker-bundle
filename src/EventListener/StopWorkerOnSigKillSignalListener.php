<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerStartedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Tobias Schultze <http://tobion.de>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkerOnSigKillSignalListener implements EventSubscriberInterface
{
    public function onWorkerStarted(WorkerStartedEvent $event): void
    {
        $handler = static function () use ($event) {
            $event->getWorker()->stop();
        };

        pcntl_signal(\SIGTERM, $handler);
        pcntl_signal(\SIGQUIT, $handler);
        pcntl_signal(\SIGINT, $handler);
        pcntl_signal(\SIGHUP, $handler);
        pcntl_signal(\SIGUSR1, $handler);
    }

    public static function getSubscribedEvents()
    {
        if (!\function_exists('pcntl_signal')) {
            return [];
        }

        return [
            WorkerStartedEvent::class => ['onWorkerStarted', 100],
        ];
    }
}
