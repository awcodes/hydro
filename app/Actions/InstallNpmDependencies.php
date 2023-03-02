<?php

namespace App\Actions;

use App\Actions\Concerns\AbortsCommands;
use App\ConsoleWriter;
use App\Shell;
use Exception;

class InstallNpmDependencies
{
    use AbortsCommands;

    public function __construct(
        protected Shell $shell,
        protected ConsoleWriter $consoleWriter
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        if ($this->consoleWriter->confirm('Install NPM dependencies?')) {
            $this->consoleWriter->logStep('Installing node dependencies...');

            $process = $this->shell->execInProject("npm install{$this->withQuiet()}");
            $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);

            $this->consoleWriter->success('Npm dependencies installed.');
            $this->consoleWriter->newLine();
        }
    }

    public function withQuiet(): string
    {
        return config('hydro.store.with_output') ? '' : ' --silent';
    }
}
