<?php

namespace App\Config;

use App\Commands\Debug;
use App\Commands\NewCommand;
use App\ConsoleWriter;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetConfig
{
    use Debug;

    protected ConsoleWriter $consoleWriter;

    protected array $options;

    private CommandLineConfiguration $commandLineConfiguration;

    private SavedConfiguration $savedConfiguration;

    private ShellConfiguration $shellConfiguration;

    private array $commandLineInput;

    public function __construct(
       CommandLineConfiguration $commandLineConfiguration,
       SavedConfiguration $savedConfiguration,
       ShellConfiguration $shellConfiguration,
       ConsoleWriter $consoleWriter,
       InputInterface $commandLineOptions
    ) {
        $this->commandLineConfiguration = $commandLineConfiguration;
        $this->savedConfiguration = $savedConfiguration;
        $this->shellConfiguration = $shellConfiguration;
        $this->consoleWriter = $consoleWriter;

        $this->commandLineInput = array_filter($commandLineOptions->getOptions(), function ($value, $key) use ($commandLineOptions) {
            return $commandLineOptions->hasParameterOption("--$key");
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function __invoke($defaultConfiguration): void
    {
        foreach ($defaultConfiguration as $configurationKey => $default) {
            $methodName = 'get'.Str::of($configurationKey)->studly();
            if (method_exists($this, $methodName)) {
                config(["filament-plugin.store.$configurationKey" => $this->$methodName($configurationKey,
                    $default)]);
            } else {
                config(["filament-plugin.store.$configurationKey" => $this->get($configurationKey, $default)]);
            }
        }

        if (config('filament-plugin.store.command') === NewCommand::class) {
            $projectPath = config('filament-plugin.store.root_path').'/'.Str::of(config('filament-plugin.store.plugin_name'))->slug();
            config(['filament-plugin.store.project_path' => $projectPath]);
        }
    }

    private function get(string $configurationKey, $default)
    {
        if (isset($this->commandLineConfiguration->$configurationKey)) {
            return $this->commandLineConfiguration->$configurationKey;
        }

        if (isset($this->savedConfiguration->$configurationKey)) {
            return $this->savedConfiguration->$configurationKey;
        }

        if (isset($this->shellConfiguration->$configurationKey)) {
            return $this->shellConfiguration->$configurationKey;
        }

        return $default;
    }

    private function getWithOutput(string $key, $default): bool
    {
        if ($this->consoleWriter->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            return true;
        }

        return $this->get($key, $default);
    }
}
