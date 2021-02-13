<?php

namespace Oka\WorkerBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class RunWorkerCommandTest extends KernelTestCase
{
    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $commandTester;
    
    public function setUp(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        
        $command = $application->find('oka:worker:run-worker');
        $this->commandTester = new CommandTester($command);
    }
    
    public function tearDown(): void
    {
        $this->commandTester = null;
    }
    
    /**
     * @covers
     */
    public function testThatRunWorkerWithTimeLimitOfThreeSeconds()
    {
        $this->commandTester->execute([
            'workerName' => 'noop',
            '--time-limit' => 3
        ]);
        
        $output = $this->commandTester->getDisplay();
        $this->assertContains('been running for 3s', $output);
    }
}
