<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Oka\WorkerBundle\Event\WorkerStartedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Simon Delicata <simon.delicata@free.fr>
 * @author Tobias Schultze <http://tobion.de>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkerOnTimeLimitListener implements EventSubscriberInterface
{
    private $timeLimitInSeconds;
    private $logger;
    private $endTime;

    public function __construct(int $timeLimitInSeconds, ?LoggerInterface $logger = null)
    {
        $this->timeLimitInSeconds = $timeLimitInSeconds;
        $this->logger = $logger;
    }

    public function onWorkerStarted(): void
    {
        $this->endTime = microtime(true) + $this->timeLimitInSeconds;
    }

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        if ($this->endTime < microtime(true)) {
            $event->getWorker()->stop();

            if (null !== $this->logger) {
                $this->logger->info('Worker ({workerName}) stopped due to time limit of {timeLimit}s exceeded', [
                    'workerName' => $event->getWorker()::getName(),
                    'timeLimit' => $this->timeLimitInSeconds,
                ]);
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
}
