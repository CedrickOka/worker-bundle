<?php

namespace Oka\WorkerBundle;

use Oka\WorkerBundle\DependencyInjection\Compiler\CachePoolServicePass;
use Oka\WorkerBundle\DependencyInjection\Compiler\LoggerServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class OkaWorkerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new CachePoolServicePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
        $container->addCompilerPass(new LoggerServicePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 5);
    }
}
