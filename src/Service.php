<?php

/*
 * This file is part of the systemctl PHP library.
 *
 * (c) Martin Janser <martin@duss-janser.ch>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SystemCtl;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class Service
{
    const STATUS_STOPPED = 3;

    /**
     * @var string
     */
    private static $command = 'systemctl';

    /**
     * @var bool
     */
    private static $sudo = true;

    /**
     * @var string
     */
    private $name;

    /**
     * Sets the systemctl command to use.
     *
     * @param string $command
     */
    public static function setCommand($command)
    {
        self::$command = $command;
    }

    /**
     * Specifies whether or not to use sudo to run the systemctl command.
     *
     * @param bool $flag
     */
    public static function sudo($flag = true)
    {
        self::$sudo = (bool) $flag;
    }

    /**
     * @param string $name Name of the service to manage
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Checks whether or not the service is running.
     *
     * @throws CommandFailedException If the command failed.
     *
     * @return bool
     */
    public function isRunning()
    {
        $builder = $this->getProcessBuilder();
        $builder->add('--lines=0')->add('status')->add($this->name);

        $process = $builder->getProcess();

        $process->run();

        if ($process->isSuccessful()) {
            return true;
        }
        if (self::STATUS_STOPPED === $process->getExitCode()) {
            return false;
        }

        throw new CommandFailedException($process);
    }

    /**
     * Starts the service.
     *
     * @throws CommandFailedException If the command failed.
     */
    public function start()
    {
        if ($this->isRunning()) {
            return;
        }

        $builder = $this->getProcessBuilder();
        $builder->add('start')->add($this->name);

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CommandFailedException($process);
        }
    }

    /**
     * Stops the service.
     *
     * @throws CommandFailedException If the command failed.
     */
    public function stop()
    {
        if (!$this->isRunning()) {
            return;
        }

        $builder = $this->getProcessBuilder();
        $builder->add('stop')->add($this->name);

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CommandFailedException($process);
        }
    }

    /**
     * Restarts the service.
     *
     * @throws CommandFailedException If the command failed.
     */
    public function restart()
    {
        $builder = $this->getProcessBuilder();
        $builder->add('restart')->add($this->name);

        $process = $builder->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new CommandFailedException($process);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Creates and prepares a process builder.
     *
     * @return ProcessBuilder
     */
    private function getProcessBuilder()
    {
        $command = explode(' ', self::$command);
        if (self::$sudo) {
            array_unshift($command, 'sudo');
        }

        $builder = ProcessBuilder::create($command);
        $builder->setTimeout(3);

        return $builder;
    }
}
