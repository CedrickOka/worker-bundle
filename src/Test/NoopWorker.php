<?php

namespace Oka\WorkerBundle\Test;

use Oka\WorkerBundle\AbstractWorker;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class NoopWorker extends AbstractWorker
{
    public static function getName(): string
    {
        return 'noop';
    }

    protected function doRun(): bool
    {
        return false;
    }
}
