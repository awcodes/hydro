<?php

namespace App;

use Illuminate\Contracts\Config\Repository;
use Symfony\Component\Process\Process;

class Shell
{
    protected ?string $rootPath;

    protected ?string $projectPath;

    protected ConsoleWriter $consoleWriter;

    private bool $useTTY = false;

    public function __construct(Repository $config, ConsoleWriter $consoleWriter)
    {
        $this->rootPath = $config->get('filament-plugin.store.root_path');
        $this->projectPath = $config->get('filament-plugin.store.project_path');
        $this->consoleWriter = $consoleWriter;
    }

    public function execInRoot(string $command): Process
    {
        return $this->exec("cd $this->rootPath && $command");
    }

    public function execInProject(string $command): Process
    {
        return $this->exec("cd $this->projectPath && $command");
    }

    public function execIn(string $directory, string $command): Process
    {
        return $this->exec("cd $directory && $command");
    }

    public function execQuietlyInRoot(string $command): Process
    {
        return $this->execQuietly("cd $this->rootPath && $command");
    }

    public function execQuietlyInProject(string $command): Process
    {
        return $this->execQuietly("cd $this->projectPath && $command");
    }

    public function execQuietlyIn(string $directory, string $command): Process
    {
        return $this->execQuietly("cd $directory && $command");
    }

    public function exec(string $command): Process
    {
        if ($this->consoleWriter->isDebug()) {
            $this->consoleWriter->exec($command);
        }

        $process = Process::fromShellCommandline($command)
            ->setTty($this->useTTY)
            ->setTimeout(null)
            ->enableOutput();

        $process->run(function ($type, $buffer) {
            if (empty(trim($buffer)) || $buffer === PHP_EOL) {
                return;
            }

            foreach (explode(PHP_EOL, trim($buffer)) as $line) {
                $this->consoleWriter->consoleOutput($line, $type);
            }
        });

        $this->useTTY = false;

        return $process;
    }

    public function execQuietly(string $command): Process
    {
        $process = Process::fromShellCommandline($command)
            ->setTimeout(null)
            ->enableOutput();

        $process->run();

        return $process;
    }

    public function withTTY(): static
    {
        $this->useTTY = true;

        return $this;
    }
}
