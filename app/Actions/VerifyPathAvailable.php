<?php

namespace App\Actions;

use App\ConsoleWriter;
use Exception;
use Illuminate\Support\Facades\File;

class VerifyPathAvailable
{
    use AbortsCommands;

    public function __construct(
         protected ConsoleWriter $consoleWriter
     ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->consoleWriter->logStep('Verifying path availability...');

        $rootPath = config('filament-plugin.store.root_path');

        if (! File::isDirectory($rootPath)) {
            throw new Exception("{$rootPath} is not a directory.");
        }

        $projectPath = config('filament-plugin.store.project_path');

        if (empty($projectPath)) {
            throw new Exception("Configuration 'filament-plugin.store.project_path' cannot be null or an empty string.");
        }

        if (File::isDirectory($projectPath)) {
            if (! config('filament-plugin.store.force_create')) {
                throw new Exception("{$projectPath} is already a directory.");
            }

            if (! File::deleteDirectory($projectPath)) {
                throw new Exception("{$projectPath} is already a directory and, although the force option was specified, deletion failed.");
            }
        }

        $this->consoleWriter->success("Directory '$projectPath' is available.");
    }
}
