<?php

namespace Oka\WorkerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('oka_worker');
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        
        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('cache_pool_id')
                        ->defaultValue('cache.app')
                        ->info('The ID of the cache pool service to use.')
                    ->end()
                    ->scalarNode('logger_id')
                        ->defaultValue('logger')
                        ->info('The ID of the logger service to use.')
                    ->end()
                ->end();
        
        return $treeBuilder;
    }
}
