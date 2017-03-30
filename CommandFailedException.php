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

namespace SystemCtl;

use Symfony\Component\Process\Process;

class CommandFailedException extends \RuntimeException
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;

        $message = sprintf(
            'Command "%s" failed with code %s, error returned: %s',
            $this->process->getCommandLine(),
            $this->process->getExitCode(),
            $this->process->getErrorOutput()
        );

        parent::__construct($message, $this->process->getExitCode());
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}
