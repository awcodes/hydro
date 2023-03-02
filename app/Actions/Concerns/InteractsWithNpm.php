<?php

namespace App\Actions\Concerns;

use App\Shell;
use Exception;

trait InteractsWithNpm
{
    use AbortsCommands;

    /**
     * @throws Exception
     */
    protected function installAndCompileNodeDependencies(): void
    {
        $this->installNodeDependencies();
        $this->compileNodeDependencies();
    }

    /**
     * @throws Exception
     */
    private function installNodeDependencies(): void
    {
        $process = app(Shell::class)->execInProject('npm install'.(config('hydro.store.with_output') ? ''
                : ' --silent'));
        $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);
    }

    /**
     * @throws Exception
     */
    private function compileNodeDependencies(): void
    {
        $process = app(Shell::class)->execInProject('npm run build'.(config('hydro.store.with_output') ? '' : ' --silent'));
        $this->abortIf(! $process->isSuccessful(), 'Compilation of project assets did not complete successfully', $process);
    }
}
