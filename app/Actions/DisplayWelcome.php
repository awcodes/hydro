<?php

namespace App\Actions;

class DisplayWelcome
{
    protected string $logo = '
    ______  __                           __ :version:
   / ____(_) /___   ___ __   ___  ____  / /_
  / /_  /\/ / __ `/ __ `__ \/ _ \/ __ \/ __/
 / __/ /\/ / /_/ / / / / / /  __/ / / / /_
/_/   /\/_/\__,_/_/ /_/ /_/\___/_/ /_/\__/';

    public function __construct()
    {
        $this->logo = str_replace(':version:', config('app.version'), $this->logo);
    }

    public function __invoke(): void
    {
        foreach (explode("\n", $this->logo) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console-writer')->text("<fg=#eab308;bg=default>{$line} </>");
        }
    }
}
