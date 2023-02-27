<?php

namespace App\Config;

class ShellConfiguration extends HydroConfiguration
{
    protected function getSettings(): array
    {
        return $_SERVER;
    }
}
