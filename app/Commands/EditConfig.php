<?php

namespace App\Commands;

use App\Actions\EditConfigFile;
use App\Config\CommandLineConfiguration;
use App\Config\FilamentPluginConfiguration;
use App\Config\SavedConfiguration;
use App\Config\SetConfig;
use App\Config\ShellConfiguration;
use Exception;

class EditConfig extends BaseCommand
{
    protected $signature = 'edit-config {--editor= : Open the config file in the specified <info>EDITOR</info> or the system default if none is specified.}';

    protected $description = 'Edit Config File. A new config file is created if one does not already exist.';

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        $commandLineConfiguration = new CommandLineConfiguration([
            'editor' => FilamentPluginConfiguration::EDITOR,
        ]);

        $savedConfiguration = new SavedConfiguration([
            'CODEEDITOR' => FilamentPluginConfiguration::EDITOR,
        ]);

        $shellConfiguration = new ShellConfiguration([
            'EDITOR' => FilamentPluginConfiguration::EDITOR,
        ]);

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration,
            app('console-writer'),
            $this->input
        ))([
            FilamentPluginConfiguration::EDITOR => 'nano',
        ]);

        try {
            app(EditConfigFile::class)('config');
        } catch (Exception $e) {
            app('console-writer')->exception($e->getMessage());
        }
    }
}
