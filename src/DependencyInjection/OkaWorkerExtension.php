<?php

namespace Oka\WorkerBundle\DependencyInjection;

use Oka\WorkerBundle\WorkerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class OkaWorkerExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
	    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
	    $loader->load('services.yml');
	    
	    $container
    	    ->registerForAutoconfiguration(WorkerInterface::class)
    	    ->addTag('oka_worker.worker');
	    
	    $configuration = new Configuration();
	    $config = $this->processConfiguration($configuration, $configs);
	    
	    $container->setParameter('oka_worker.cache_pool_id', $config['cache_pool_id']);
	    $container->setParameter('oka_worker.logger_id', $config['logger_id']);
	}
}
