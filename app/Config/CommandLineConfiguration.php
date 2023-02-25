<?php

namespace App\Config;

class CommandLineConfiguration extends FilamentPluginConfiguration
{
    protected function getSettings(): array
    {
        $commandLineConfiguration = app('console')->options();

        foreach (app('console')->arguments() as $key => $value) {
            $commandLineConfiguration[$key] = $value;
        }

        return $commandLineConfiguration;
    }
}
