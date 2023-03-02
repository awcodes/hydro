<?php

namespace App\Actions\Concerns;

use Exception;

trait AbortsCommands
{
    /**
     * @throws Exception
     */
    public function abortIf(bool $abort, string $message, $process = null): void
    {
        if ($abort) {
            if ($process) {
                throw new Exception("{$message}\nFailed to run: '{$process->getCommandLine()}'.");
            }

            throw new Exception("{$message}");
        }
    }
}
