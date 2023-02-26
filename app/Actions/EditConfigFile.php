<?php

namespace App\Actions;

use App\Shell;
use Exception;
use Illuminate\Support\Facades\File;

class EditConfigFile
{
    use AbortsCommands;
    public function __construct(
        protected Shell $shell
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(string $fileName): void
    {
        $configDir = config('home_dir').'/.filament-plugin';
        $configFilePath = $configDir.'/'.$fileName;

        if (! File::isDirectory($configDir)) {
            app('console-writer')->note("Configuration directory '{$configDir}' does not exist, creating it now...");
            $this->abortIf(! File::makeDirectory($configDir), "I could not create the directory: {$configDir}.");
        }

        if (! File::isFile($configFilePath)) {
            app('console-writer')->note("File '{$configFilePath}' does not exist, creating it now...");
            $this->abortIf(! File::put($configFilePath, File::get(base_path("stubs/{$fileName}"))), "I could not create the configuration file: {$configFilePath}.");
        }

        $process = $this->shell->withTTY()->execIn($configDir, config('filament-plugin.store.editor')." {$fileName}");

        $this->abortIf(! $process->isSuccessful(), "I could not open {$configFilePath} for editing.", $process);
    }
}
