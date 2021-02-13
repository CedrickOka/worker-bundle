<?php

namespace Oka\WorkerBundle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
interface WorkerInterface
{
    /**
     * Execute before run.
     */
    public function beforeRun(): void;
    
    /**
     * Execute worker logic on each loop.
     *
     * Valid options are:
     *  * sleep (default: 1000000): Time in microseconds to sleep after no messages are found
     */
    public function run(array $options = []): void;
    
    /**
     * Execute after run.
     */
    public function afterRun(): void;
    
    /**
     * Stop the worker
     */
    public function stop(): void;
    
    /**
     * Get the worker name
     */
    public static function getName(): string;
}
