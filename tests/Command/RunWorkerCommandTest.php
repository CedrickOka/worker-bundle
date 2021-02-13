<?php

namespace Oka\WorkerBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

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
    public function testThatCanRunWorkerWithTimeLimitOfThreeSeconds()
    {
        $this->commandTester->execute([
            'workerName' => 'noop',
            '--time-limit' => 1
        ]);
        
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('been running for 1s', $output);
    }
    
    /**
     * @covers
     */
    public function testThatCanRunWorkerWithMemoryLimitOfOneMegaOctet()
    {
        $this->commandTester->execute([
            'workerName' => 'noop',
            '--memory-limit' => '1M'
        ]);
        
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('exceeded 1M of memory', $output);
    }
    
    /**
     * @covers
     */
    public function testThatCanRunWorkerWithLoopLimitOfTenLoops()
    {
        $this->commandTester->execute([
            'workerName' => 'noop',
            '--limit' => '10'
        ]);
        
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('processed 10 loops', $output);
    }
    
    /**
     * @covers
     */
    public function testThatCannotRunWorkerWithNameNotFound()
    {
        $this->expectException(ServiceNotFoundException::class);
        
        $this->commandTester->execute([
            'workerName' => 'test'
        ]);
    }
}
