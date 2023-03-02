<?php

namespace App\Actions;

use App\Actions\Concerns\AbortsCommands;
use App\Actions\Concerns\InteractsWithGitHub;
use App\ConsoleWriter;
use App\Shell;
use Exception;

class InitializeGitHubRepository
{
    use AbortsCommands;
    use InteractsWithGitHub;

    public const WARNING_FAILED_TO_CREATE_REPOSITORY = 'Failed to create new GitHub repository';

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
        if (! static::gitHubInitializationRequested()) {
            return;
        }

        $this->consoleWriter->logStep('Initializing GitHub repository');

        $process = $this->shell->execInProject(static::getGitHubCreateCommand());

        if (! $process->isSuccessful()) {
            $this->consoleWriter->warn(self::WARNING_FAILED_TO_CREATE_REPOSITORY);
            $this->consoleWriter->warnCommandFailed($process->getCommandLine());
            $this->consoleWriter->showOutputErrors($process->getErrorOutput());
            config(['hydro.store.push_to_github' => false]);

            return;
        }
        config(['hydro.store.push_to_github' => true]);

        $this->consoleWriter->success('Successfully created new GitHub repository');
    }
}
