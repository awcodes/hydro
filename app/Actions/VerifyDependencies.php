<?php

namespace App\Actions;

use App\ConsoleWriter;
use Exception;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    use AbortsCommands;

    private array $dependencies = [
        [
            'command' => 'composer',
            'label' => 'Composer',
            'instructions_url' => 'https://getcomposer.org',
        ],
        [
            'command' => 'git',
            'label' => 'Git version control',
            'instructions_url' => 'https://git-scm.com',
        ],
    ];

    private array $optionalDependencies = [
        [
            'command' => 'hub',
            'label' => 'Unofficial GitHub command line tool',
            'instructions_url' => 'https://github.com/github/hub',
        ],
        [
            'command' => 'gh',
            'label' => 'Official GitHub command line tool',
            'instructions_url' => 'https://cli.github.com/',
        ],
    ];

    public function __construct(
        protected ExecutableFinder $finder,
        protected ConsoleWriter $consoleWriter
    )
    {}

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->consoleWriter->logStep('Verifying dependencies...');

        $this->consoleWriter->sectionTitle('Optional Dependencies');

        foreach ($this->optionalDependencies as $optionalDependency) {
            [$command, $label, $instructionsUrl] = array_values($optionalDependency);

            if (($installedDependency = $this->finder->find($command)) === null) {
                if ($command === 'hub' && $this->finder->find('gh') !== null) {
                    continue;
                }

                $this->consoleWriter->note("{$label}, an optional dependency, is missing. you can find installation instructions at: <a class=\"text-sky-500\" href=\"{$instructionsUrl}\">{$instructionsUrl}</a>");
            } else {
                $this->consoleWriter->success("{$label} found at: <span class=\"text-sky-600\">{$installedDependency}</span>");
            }
        }

        $this->consoleWriter->sectionTitle('Required Dependencies');

        $this->abortIf(
            collect($this->dependencies)->reduce(function ($carry, $dependency) {
                [$command, $label, $instructionsUrl] = array_values($dependency);
                if (($installedDependency = $this->finder->find($command)) === null) {
                    $this->consoleWriter->warn("{$label} is missing. You can find installation instructions at: <a class=\"text-sky-500\" href=\"{$instructionsUrl}\">{$instructionsUrl}</a>");

                    return true;
                }
                $this->consoleWriter->success("{$label} found at: <span class=\"text-sky-600\">{$installedDependency}</span>");

                return $carry ?? false;
            }),
            'Please install missing dependencies and try again.'
        );
    }
}
