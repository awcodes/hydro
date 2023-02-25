<?php

namespace App\Config;

class ShellConfiguration extends FilamentPluginConfiguration
{
    protected function getSettings(): array
    {
        return $_SERVER;
    }
}
