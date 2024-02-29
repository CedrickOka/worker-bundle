<?php

namespace Oka\WorkerBundle\EventListener;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Oka\WorkerBundle\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 * @author Tobias Schultze <http://tobion.de>
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkerOnLoopLimitListener implements EventSubscriberInterface
{
    private $maximumNumberOfLoops;
    private $logger;

    /**
     * @var int
     */
    private $processedLoops = 0;

    public function __construct(int $maximumNumberOfLoops, ?LoggerInterface $logger = null)
    {
        $this->maximumNumberOfLoops = $maximumNumberOfLoops;
        $this->logger = $logger;

        if ($maximumNumberOfLoops <= 0) {
            throw new InvalidArgumentException('Loop limit must be greater than zero.');
        }
    }

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        if (false === $event->isWorkerIdle() && ++$this->processedLoops >= $this->maximumNumberOfLoops) {
            $this->processedLoops = 0;
            $event->getWorker()->stop();

            if (null !== $this->logger) {
                $this->logger->info('Worker ({workerName}) stopped due to maximum count of {count} loops processed', [
                    'workerName' => $event->getWorker()::getName(),
                    'count' => $this->maximumNumberOfLoops,
                ]);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}
