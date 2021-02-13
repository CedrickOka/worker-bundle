<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Tobias Schultze <http://tobion.de>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class DispatchPcntlSignalListener implements EventSubscriberInterface
{
    public function onWorkerRunning(): void
    {
        pcntl_signal_dispatch();
    }

    public static function getSubscribedEvents()
    {
        if (!\function_exists('pcntl_signal_dispatch')) {
            return [];
        }

        return [
            WorkerRunningEvent::class => ['onWorkerRunning', 100],
        ];
    }
}
