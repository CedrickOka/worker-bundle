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

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var int the time in microseconds to sleep after no task processed
     */
    private $loopDelay = 1000000;

    /**
     * @var bool
     */
    private $shouldStop = false;

    public function __construct(?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $key, $defaultValue = null)
    {
        return $this->attributes[$key] ?? $defaultValue;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function addAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function removeAttribute(string $key): self
    {
        unset($this->attributes[$key]);

        return $this;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getLoopDelay(): int
    {
        return $this->loopDelay;
    }

    public function setLoopDelay(int $loopDelay): self
    {
        $this->loopDelay = $loopDelay;

        return $this;
    }

    public function beforeRun(): void
    {
    }

    public function run(): void
    {
        $this->dispatchEvent(new WorkerStartedEvent($this));

        while (false === $this->shouldStop) {
            $mustBeDeffered = (bool) $this->doRun();
            $this->dispatchEvent(new WorkerRunningEvent($this, $mustBeDeffered));

            if (true === $mustBeDeffered) {
                usleep($this->loopDelay);
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

    /**
     * @return bool returns true when no task has been processed by the worker
     */
    abstract protected function doRun(): bool;
}
