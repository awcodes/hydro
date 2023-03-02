<?php

namespace App\Actions\Concerns;

use App\Shell;
use Exception;

trait InteractsWithComposer
{
    use AbortsCommands;

    /**
     * @throws Exception
     */
    protected function composerRequire(string $package, bool $forDev = true): void
    {
        $command = $this->getComposerRequireCommand($package, $forDev);
        $composerProcess = app(Shell::class)->execInProject($command);
        $this->abortIf(! $composerProcess->isSuccessful(), 'Composer package installation failed.');
    }

    private function getComposerRequireCommand(string $package, bool $forDev): string
    {
        return sprintf(
            'composer require %s%s%s',
            $package,
            $forDev ? ' --dev' : '',
            config('hydro.with_output') ? '' : ' --quiet'
        );
    }
}
