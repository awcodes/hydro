<?php

namespace App;

class Environment
{
    public static function isMac(): bool
    {
        return PHP_OS === 'Darwin';
    }

    public static function isWin(): bool
    {
        return str_starts_with(strtoupper(PHP_OS), 'WIN');
    }
}
