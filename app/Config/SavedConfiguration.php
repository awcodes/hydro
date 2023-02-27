<?php

namespace App\Config;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\File;

class SavedConfiguration extends HydroConfiguration
{
    protected function getSettings(): array
    {
        $configurationPath = config('home_dir').'/'.config('config_dir', '.hydro');
        $configurationFile = config('config_file', 'config');

        if (! File::exists("{$configurationPath}/{$configurationFile}")) {
            return [];
        }

        return Dotenv::createMutable($configurationPath, $configurationFile)->load();
    }
}
