<?php

namespace App\Actions;

use App\ConsoleWriter;
use Illuminate\Support\Facades\File;

class CopySkeletonToProject
{
    public function __construct(
        protected ConsoleWriter $consoleWriter
    ) {
    }

    public function __invoke(): void
    {
        File::makeDirectory(config('hydro.store.project_path'));
        File::copyDirectory(__DIR__.'/../../stubs/'.config('hydro.store.target'), config('hydro.store.project_path'));
    }
}
