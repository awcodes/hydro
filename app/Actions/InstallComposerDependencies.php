<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Exception;

class InstallComposerDependencies
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
        if ($this->consoleWriter->confirm('Install Composer dependencies?')) {
            $this->consoleWriter->logStep('Installing Composer dependencies...');

            $process = $this->shell->execInProject("composer install{$this->withQuiet()}");
            $this->abortIf(! $process->isSuccessful(), 'Installation of Composer dependencies did not complete successfully', $process);

            $this->consoleWriter->success('Composer dependencies installed.');
            $this->consoleWriter->newLine();
        }
    }

    public function withQuiet(): string
    {
        return config('hydro.store.with_output') ? '' : ' --quiet';
    }
}
