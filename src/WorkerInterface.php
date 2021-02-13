<?php

namespace Oka\WorkerBundle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
interface WorkerInterface
{
    /**
     * Receive the messages and dispatch them to the bus.
     *
     * Valid options are:
     *  * sleep (default: 1000000): Time in microseconds to sleep after no messages are found
     */
    public function run(array $options = []): void;
    
    /**
     * Stop the worker
     */
    public function stop(): void;
    
    /**
     * Get the worker name
     */
    public static function getName(): string;
}
