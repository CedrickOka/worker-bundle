# Getting Started With OkaWorkerBundle

This bundle help the user input high quality data into your web services REST.

## Prerequisites

The OkaWorkerBundle has the following requirements:

 - PHP 7.2+
 - Symfony 4.4+

## Installation

Installation is a quick (I promise!) 3 step process:

1. Download OkaWorkerBundle
2. Register the Bundle
3. Use bundle and enjoy!

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require coka/worker-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Register the Bundle

Then, register the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project (Flex did it automatically):

```php
return [
    //...
    Oka\WorkerBundle\OkaWorkerBundle::class => ['all' => true],
]
```

### Step 3: Use the bundle is simple

Now that the bundle is installed.

Create worker class.

```php
// App\Worker\Worker.php

namespace App\Worker;

use Oka\WorkerBundle\AbstractWorker;

class FooWorker extends AbstractWorker
{
    public static function getName(): string
	{
		return 'foo';
	}
	
    protected function doRun(array $options = []): bool
	{
		//...
	}
}
```

Use the command line interface.

```sh
php bin/console oka:worker:run-worker foo
```
