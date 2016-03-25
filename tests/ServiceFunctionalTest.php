<?php

/*
 * This file is part of the systemctl PHP library.
 *
 * (c) Martin Janser <martin@duss-janser.ch>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SystemCtl\tests;

use SystemCtl\CommandFailedException;
use SystemCtl\Service;

/**
 * @covers SystemCtl\Service::__construct
 * @covers SystemCtl\Service::<private>
 *
 * @group functional
 */
class ServiceFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers SystemCtl\Service::isRunning
     */
    public function testIsRunning()
    {
        $this->startService('php-test');

        $service = new Service('php-test');

        $this->assertTrue($service->isRunning(), 'Service should be running');
    }

    /**
     * @covers SystemCtl\Service::isRunning
     */
    public function testIsNotRunning()
    {
        $this->stopService('php-test');

        $service = new Service('php-test');

        $this->assertFalse($service->isRunning(), 'Service should not be running');
    }

    /**
     * @covers SystemCtl\Service::isRunning
     * @covers SystemCtl\Service::start
     */
    public function testStart()
    {
        $this->stopService('php-test');

        $service = new Service('php-test');

        $service->start();

        $this->assertTrue($service->isRunning(), 'Service should be running');
    }

    /**
     * @covers SystemCtl\Service::isRunning
     * @covers SystemCtl\Service::start
     * @covers SystemCtl\CommandFailedException
     */
    public function testStartInvalidServiceThrowsException()
    {
        $service = new Service('php-foo');

        $this->expectException(CommandFailedException::class);

        $service->start();
    }

    /**
     * @covers SystemCtl\Service::isRunning
     * @covers SystemCtl\Service::stop
     */
    public function testStop()
    {
        $this->startService('php-test');

        $service = new Service('php-test');

        $service->stop();

        $this->assertFalse($service->isRunning(), 'Service should not be running');
    }

    /**
     * @covers SystemCtl\Service::isRunning
     * @covers SystemCtl\Service::restart
     */
    public function testRestart()
    {
        $this->startService('php-test');

        $service = new Service('php-test');

        $service->restart();

        $this->assertTrue($service->isRunning(), 'Service should be running');
    }

    /**
     * @covers SystemCtl\Service::isRunning
     * @covers SystemCtl\Service::restart
     * @covers SystemCtl\CommandFailedException
     */
    public function testRestartInvalidServiceThrowsException()
    {
        $service = new Service('php-foo');

        $this->expectException(CommandFailedException::class);

        $service->restart();
    }

    /**
     * Starts the service before testing.
     *
     * @param string $name
     */
    private function startService($name)
    {
        exec('sudo systemctl start '.$name);
    }

    /**
     * Stops the service before testing.
     *
     * @param string $name
     */
    private function stopService($name)
    {
        exec('sudo systemctl stop '.$name);
    }
}
