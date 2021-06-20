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
    private $tags = [];
    private $shouldStop = false;
    
    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function getTags(): array
    {
        return $this->tags;
    }
    
    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        
        return $this;
    }
    
    public function beforeRun(array $options = []): void
    {
    }
    
    public function run(array $options = []): void
    {
        $options = array_merge(['sleep' => 1000000], $options);
        
        $this->dispatchEvent(new WorkerStartedEvent($this, $options));
        
        while (false === $this->shouldStop) {
            $mustBeDeffered = (bool) $this->doRun($options);
            
            $this->dispatchEvent(new WorkerRunningEvent($this, $mustBeDeffered));
            
            if (true === $mustBeDeffered) {
                usleep($options['sleep']);
            }
        }
        
        $this->dispatchEvent(new WorkerStoppedEvent($this));
    }
    
    public function afterRun(array $options = []): void
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
