<?php

namespace Oka\WorkerBundle\Test;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 *
 * @author Cedrick Oka Baidai <baidai.cedric@veone.net>
 *
 */
class AppKernel extends Kernel
{
	public function registerBundles()
	{
		$bundles = [
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
		    new \Oka\WorkerBundle\OkaWorkerBundle()
		];
		
		return $bundles;
	}
	
	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		// We don't need that Environment stuff, just one config
		$loader->load(__DIR__.'/config.yaml');
	}
}
