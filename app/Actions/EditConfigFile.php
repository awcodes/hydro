<?php

namespace App\Actions;

use App\Actions\Concerns\AbortsCommands;
use App\ConsoleWriter;
use App\Shell;
use Exception;
use Illuminate\Support\Facades\File;

class EditConfigFile
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
    public function __invoke(string $fileName): void
    {
        $configDir = config('home_dir').'/.hydro';
        $configFilePath = $configDir.'/'.$fileName;

        if (! File::isDirectory($configDir)) {
            $this->consoleWriter->note("Configuration directory '{$configDir}' does not exist, creating it now...");
            $this->abortIf(! File::makeDirectory($configDir), "I could not create the directory: {$configDir}.");
        }

        if (! File::isFile($configFilePath)) {
            $this->consoleWriter->note("File '{$configFilePath}' does not exist, creating it now...");
            $this->abortIf(! File::put($configFilePath, File::get(base_path("stubs/{$fileName}"))), "I could not create the configuration file: {$configFilePath}.");
        }

        $process = $this->shell->withTTY()->execIn($configDir, config('hydro.store.editor')." {$fileName}");

        $this->abortIf(! $process->isSuccessful(), "I could not open {$configFilePath} for editing.", $process);
    }
}
