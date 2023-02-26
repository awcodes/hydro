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
        File::makeDirectory(config('filament-plugin.store.project_path'));
        File::copyDirectory(__DIR__.'/../../stubs/'.config('filament-plugin.store.target'), config('filament-plugin.store.project_path'));
    }
}
