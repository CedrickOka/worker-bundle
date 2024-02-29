<?php

namespace Oka\WorkerBundle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
interface WorkerInterface
{
    /**
     * Gets worker identifier.
     */
    public function getId(): string;

    /**
     * Gets worker tags list.
     */
    public function getTags(): array;

    /**
     * Gets worker attributes list.
     */
    public function getAttributes(): array;

    /**
     * Execute before run.
     */
    public function beforeRun(): void;

    /**
     * Execute worker logic on each loop.
     */
    public function run(): void;

    /**
     * Execute after run.
     */
    public function afterRun(): void;

    /**
     * Stop the worker.
     */
    public function stop(): void;

    /**
     * Get the worker name.
     */
    public static function getName(): string;
}
