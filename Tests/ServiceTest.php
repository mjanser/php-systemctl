<?php

declare(strict_types=1);

/*
 * This file is part of the systemctl PHP library.
 *
 * (c) Martin Janser <martin@duss-janser.ch>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SystemCtl\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use SystemCtl\CommandFailedException;
use SystemCtl\Service;

/**
 * @covers \SystemCtl\Service::__construct
 * @covers \SystemCtl\Service::setCommand
 * @covers \SystemCtl\Service::sudo
 * @covers \SystemCtl\Service::<private>
 */
class ServiceTest extends TestCase
{
    /**
     * @var string
     */
    private $commandFilename;

    /**
     * @var string
     */
    private $callCountFilename;

    /**
     * @var int
     */
    private $callCount = 1;

    protected function setUp()
    {
        $this->callCount = 1;
    }

    protected function tearDown()
    {
        if ($this->commandFilename) {
            unlink($this->commandFilename);
            $this->commandFilename = null;
        }
        if ($this->callCountFilename) {
            unlink($this->callCountFilename);
            $this->callCountFilename = null;
        }
    }

    /**
     * @covers \SystemCtl\Service::__toString
     */
    public function testServiceName()
    {
        $service = $this->getMockedService('test-service');

        $this->expectNoOtherCalls();

        $this->assertSame('test-service', (string) $service, 'Service name should match');
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     */
    public function testIsRunning()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], 0);
        $this->expectNoOtherCalls();

        $this->assertTrue($service->isRunning(), 'Service should be running');
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     */
    public function testIsNotRunning()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], Service::STATUS_STOPPED);
        $this->expectNoOtherCalls();

        $this->assertFalse($service->isRunning(), 'Service should not be running');
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\CommandFailedException
     */
    public function testCheckInvalidServiceThrowsException()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['status', 'test-service'], 6);
        $this->expectNoOtherCalls();
        $this->expectException(CommandFailedException::class);

        $service->isRunning();
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::start
     */
    public function testStart()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], Service::STATUS_STOPPED);
        $this->expectCall(['start', 'test-service'], 0);
        $this->expectNoOtherCalls();

        $service->start();

        $this->assertTrue(true);
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::start
     */
    public function testStartAlreadyRunning()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], 0);
        $this->expectNoOtherCalls();

        $service->start();

        $this->assertTrue(true);
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::start
     * @covers \SystemCtl\CommandFailedException
     */
    public function testStartInvalidServiceThrowsException()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], Service::STATUS_STOPPED);
        $this->expectCall(['start', 'test-service'], 6);
        $this->expectNoOtherCalls();
        $this->expectException(CommandFailedException::class);

        $service->start();
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::stop
     */
    public function testStop()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], 0);
        $this->expectCall(['stop', 'test-service'], 0);
        $this->expectNoOtherCalls();

        $service->stop();

        $this->assertTrue(true);
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::stop
     */
    public function testStopWhenNotRunning()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], Service::STATUS_STOPPED);
        $this->expectNoOtherCalls();

        $service->stop();

        $this->assertTrue(true);
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::stop
     * @covers \SystemCtl\CommandFailedException
     */
    public function testStopInvalidServiceThrowsException()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['--lines=0', 'status', 'test-service'], 0);
        $this->expectCall(['stop', 'test-service'], 6);
        $this->expectNoOtherCalls();
        $this->expectException(CommandFailedException::class);

        $service->stop();
    }

    /**
     * @covers \SystemCtl\Service::restart
     */
    public function testRestart()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['restart', 'test-service'], 0);
        $this->expectNoOtherCalls();

        $service->restart();

        $this->assertTrue(true);
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::restart
     * @covers \SystemCtl\CommandFailedException
     */
    public function testRestartInvalidServiceThrowsException()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['restart', 'test-service'], 6);
        $this->expectNoOtherCalls();
        $this->expectException(CommandFailedException::class);

        $service->restart();
    }

    /**
     * @covers \SystemCtl\Service::isRunning
     * @covers \SystemCtl\Service::restart
     * @covers \SystemCtl\CommandFailedException
     */
    public function testExceptionHasProcess()
    {
        $service = $this->getMockedService('test-service');

        $this->expectCall(['restart', 'test-service'], 6);
        $this->expectNoOtherCalls();

        try {
            $service->restart();

            $this->fail('Command should fail');
        } catch (CommandFailedException $e) {
            $this->assertInstanceOf(Process::class, $e->getProcess());
        }
    }

    /**
     * Returns a service instance with a mocked systemctl command.
     *
     * @param string $name Service name
     *
     * @return Service
     */
    private function getMockedService($name)
    {
        $this->commandFilename = tempnam(sys_get_temp_dir(), 'systemctl');
        $this->callCountFilename = tempnam(sys_get_temp_dir(), 'systemctl');

        file_put_contents($this->callCountFilename, '0');

        file_put_contents($this->commandFilename, '<?php'."\n");
        file_put_contents($this->commandFilename, sprintf(
            '$c = file_get_contents(\'%s\');'."\n",
            $this->callCountFilename
        ), FILE_APPEND);
        file_put_contents($this->commandFilename, sprintf(
            'file_put_contents(\'%s\', ++$c);'."\n",
            $this->callCountFilename
        ), FILE_APPEND);

        Service::setCommand('php '.$this->commandFilename);
        Service::sudo(false);

        return new Service($name);
    }

    /**
     * Adds an expected call to the systemctl command.
     *
     * @param string[] $arguments List of expected arguments
     * @param int      $exitCode  Exit code which the command should return
     */
    private function expectCall(array $arguments, $exitCode)
    {
        $conditions = [];
        $index = 1;
        foreach ($arguments as $argument) {
            $conditions[] = sprintf(
                'isset($argv[%1$d]) && $argv[%1$d] === \'%2$s\'',
                $index++,
                $argument
            );
        }

        $code = sprintf(
            'if (%d == $c && %s) { exit(%d); }'."\n",
            $this->callCount++,
            implode(' && ', $conditions),
            $exitCode
        );

        file_put_contents($this->commandFilename, $code, FILE_APPEND);
    }

    /**
     * Sets no more expected calls to the systemctl command.
     */
    private function expectNoOtherCalls()
    {
        $code = 'fwrite(STDERR, "Invalid call count or arguments specified: ".$c.", ".var_export($argv, true)); exit(250);'."\n";

        file_put_contents($this->commandFilename, $code, FILE_APPEND);
    }
}
