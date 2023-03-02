<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Options;

class DisplayHelpScreen
{
    public function __construct(
        protected ConsoleWriter $consoleWriter
    ) {
    }

    public function __invoke(): void
    {
        $this->consoleWriter->text("\n <comment>Usage:</comment>{$this->createCliStringForCommandUsage()}");
        $this->consoleWriter->text("\n <comment>Options (hydro new myPlugin):</comment>{$this->createCliStringForOptionDescriptions()}");
    }

    public function createCliStringForOptionDescriptions(): string
    {
        return collect((new Options())->all())->reduce(function ($carry, $option) {
            $flag = isset($option['short'])
                ? '-'.$option['short'].', --'.$option['long']
                : '    --'.$option['long'];

            $flag .= isset($option['param_description'])
                ? "={$option['param_description']}"
                : '';

            return $carry.sprintf("\n   <info>%-33s</info>%s", $flag, $option['cli_description']);
        });
    }

    private function createCliStringForCommandUsage(): string
    {
        return collect([
            [
                'usage' => 'hydro new myPlugin [options]',
                'description' => 'Scaffold a new Filament plugin',
            ],
        ])->reduce(function ($carry, $command) {
            return $carry.sprintf("\n   <info>%-32s</info> %s", $command['usage'], $command['description']);
        });
    }
}
