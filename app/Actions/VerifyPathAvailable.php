<?php

namespace App\Actions;

use App\Actions\Concerns\AbortsCommands;
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

        $rootPath = config('hydro.store.root_path');

        if (! File::isDirectory($rootPath)) {
            throw new Exception("$rootPath is not a directory.");
        }

        $projectPath = config('hydro.store.project_path');

        if (empty($projectPath)) {
            throw new Exception("Configuration 'hydro.store.project_path' cannot be null or an empty string.");
        }

        if (File::isDirectory($projectPath)) {
            if (! config('hydro.store.force_create')) {
                throw new Exception("$projectPath is already a directory.");
            }

            if (! File::deleteDirectory($projectPath)) {
                throw new Exception("$projectPath is already a directory and, although the force option was specified, deletion failed.");
            }
        }

        $this->consoleWriter->success("Directory <span class=\"text-sky-500\">$projectPath</span> is available.");
    }
}
