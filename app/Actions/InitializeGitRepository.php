<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Exception;

class InitializeGitRepository
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
        if ($this->consoleWriter->confirm('Initialize git repository?')) {
            $this->consoleWriter->logStep('Initializing git repository...');

            $this->exec(sprintf(
                'git init%s',
                config('hydro.store.with_output') ? '' : ' --quiet',
            ));

            $this->exec('git add .');

            $this->exec(sprintf(
                "git commit%s -m '%s'",
                config('hydro.store.with_output') ? '' : ' --quiet',
                config('hydro.store.commit_message')
            ));

            $this->consoleWriter->success('New git repository initialized.');
            $this->consoleWriter->newLine();
        }
    }

    /**
     * @throws Exception
     */
    public function exec($command): void
    {
        $process = $this->shell->execInProject($command);
        $this->abortIf(! $process->isSuccessful(), 'Initialization of git repository did not complete successfully.', $process);
    }
}
