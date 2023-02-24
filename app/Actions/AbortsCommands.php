<?php

namespace App\Actions;

use App\InstallerException;

trait AbortsCommands
{
    /**
     * @throws InstallerException
     */
    public function abortIf(bool $abort, string $message, $process = null): void
    {
        if ($abort) {
            if ($process) {
                throw new InstallerException("{$message}\nFailed to run: '{$process->getCommandLine()}'.");
            }

            throw new InstallerException("{$message}");
        }
    }
}
