<?php

namespace Oka\WorkerBundle;

use Oka\WorkerBundle\Event\WorkerRunningEvent;
use Oka\WorkerBundle\Event\WorkerStartedEvent;
use Oka\WorkerBundle\Event\WorkerStoppedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
abstract class AbstractWorker implements WorkerInterface
{
    private $eventDispatcher;
    private $shouldStop = false;
    
    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function beforeRun(): void
    {
    }
    
    public function run(array $options = []): void
    {
        $options = array_merge([
            'sleep' => 1000000,
        ], $options);
        
        $this->dispatchEvent(new WorkerStartedEvent($this));
        
        while (false === $this->shouldStop) {
            $mustBeDeffered = $this->doRun($options);
            
            $this->dispatchEvent(new WorkerRunningEvent($this, (bool) $mustBeDeffered));
            
            if (true === $mustBeDeffered) {
                usleep($options['sleep']);
            }
        }
        
        $this->dispatchEvent(new WorkerStoppedEvent($this));
    }
    
    public function afterRun(): void
    {
    }
    
    public function stop(): void
    {
        $this->shouldStop = true;
    }
    
    private function dispatchEvent($event)
    {
        if (null === $this->eventDispatcher) {
            return;
        }
        
        $this->eventDispatcher->dispatch($event);
    }
    
    protected abstract function doRun(array $options = []): bool;
}
