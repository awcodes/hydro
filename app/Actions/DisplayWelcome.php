<?php

namespace App\Actions;

class DisplayWelcome
{
    protected string $logo = '
    ______  __                           __
   / ____(_) /___   ___ __   ___  ____  / /_
  / /_  /\/ / __ `/ __ `__ \/ _ \/ __ \/ __/
 / __/ /\/ / /_/ / / / / / /  __/ / / / /_
/_/   /\/_/\__,_/_/ /_/ /_/\___/_/ /_/\__/';

    protected string $welcomeText = "
🦒 <fg=#eab308;bg=default>Filament Plugin CLI (:version:):</> Quickly scaffold plugins for your next Filament App.";

    public function __construct()
    {
        $this->welcomeText = str_replace(':version:', config('app.version'), $this->welcomeText);
    }

    public function __invoke(): void
    {
        foreach (explode("\n", $this->logo) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console-writer')->text("<fg=#eab308;bg=default>{$line} </>");
        }

        foreach (explode("\n", $this->welcomeText) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console-writer')->text("{$line} ");
        }
    }
}
