<?php

namespace Oka\WorkerBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class StopWorkersCommandTest extends KernelTestCase
{
    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $commandTester;
    
    public function setUp(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        
        $command = $application->find('oka:worker:stop-worker');
        $this->commandTester = new CommandTester($command);
    }
    
    public function tearDown(): void
    {
        $this->commandTester = null;
    }
    
    /**
     * @covers
     */
    public function testThatCanStopWorkerWithGivenName()
    {
        $this->commandTester->execute([
            'workerName' => 'noop',
        ]);
        
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Signal successfully sent to stop any running workers.', $output);
    }
    
    /**
     * @covers
     */
    public function testThatCanStopAllWorkers()
    {
        $this->commandTester->execute([]);
        
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Signal successfully sent to stop any running workers.', $output);
    }
}
