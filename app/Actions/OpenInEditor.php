<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Exception;

class OpenInEditor
{
    use AbortsCommands;

    public function __construct(
        protected Shell $shell,
        protected ConsoleWriter $consoleWriter
    )
    {}

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->consoleWriter->logStep('Opening In Editor...');

        $process = $this->shell->withTTY()->execInProject(sprintf('%s .', config('filament-plugin.store.editor')));
        $this->abortIf(! $process->isSuccessful(), sprintf('Failed to open editor %s', config('filament-plugin.store.editor')), $process);

        $this->consoleWriter->success('Opening your project in ' . config('filament-plugin.store.editor'));
    }
}
