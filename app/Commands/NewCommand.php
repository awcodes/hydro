<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayWelcome;
use App\Actions\ValidateGitHubConfiguration;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\Config\CommandLineConfiguration;
use App\Config\FilamentPluginConfiguration;
use App\Config\SetConfig;
use App\Config\ShellConfiguration;
use App\ConsoleWriter;
use App\Options;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NewCommand extends BaseCommand
{
    use Debug;

    protected $signature;

    protected $description = 'Scaffold a new Filament plugin.';

    protected ConsoleWriter $consoleWriter;

    public function __construct()
    {
        $this->signature = $this->buildSignature();

        parent::__construct();

        app()->bind('console', function () {
            return $this;
        });
    }

    public function buildSignature()
    {
        return collect((new Options())->all())->reduce(
            function ($carry, $option) {
                return $carry . $this->buildSignatureOption($option);
            },
            "new\n{pluginName? : Name of the Filament plugin}"
        );
    }

    public function buildSignatureOption($option): string
    {
        $commandlineOption = isset($option['short']) ? ($option['short'] . '|' . $option['long']) : $option['long'];

        if (isset($option['param_description'])) {
            $commandlineOption .= '=' . ($option['default'] ?? '');
        }

        return "\n{--{$commandlineOption} : {$option['cli_description']}}";
    }

    /**
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function handle(): int
    {
        app(DisplayWelcome::class)();

        if (! $this->argument('pluginName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->setConsoleWriter();
        $this->setConfig();

//        $gitName = exec('git config user.name');
//        $authorName = $this->ask('Author name', $gitName);
//
//        $gitEmail = exec('git config user.email');
//        $authorEmail = $this->ask('Author email', $gitEmail);
//
//        $authorUsername = $this->ask('Author username', $gitName);
//
//        $vendorName = $this->ask('Vendor name', $authorUsername);
//        $vendorSlug = Str::of($vendorName)->slug()->replace('/[^A-Za-z0-9-]+/', '-')->rtrim('-');
//        $vendorNamespace = ucwords($vendorName);
//        $vendorNamespace = $this->ask('Vendor namespace', $vendorNamespace);

        app(VerifyDependencies::class)();
        app(ValidateGitHubConfiguration::class)();
        app(VerifyPathAvailable::class)();

        return self::SUCCESS;
    }

    protected function setConsoleWriter(): void
    {
        $this->consoleWriter = app(ConsoleWriter::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function setConfig(): void
    {
        config(['filament-plugin.store' => []]);

        $commandLineConfiguration = new CommandLineConfiguration([
            'editor' => FilamentPluginConfiguration::EDITOR,
            'message' => FilamentPluginConfiguration::COMMIT_MESSAGE,
            'path' => FilamentPluginConfiguration::ROOT_PATH,
            'browser' => FilamentPluginConfiguration::BROWSER,
            'force' => FilamentPluginConfiguration::FORCE_CREATE,
            'with-output' => FilamentPluginConfiguration::WITH_OUTPUT,
            'dev' => FilamentPluginConfiguration::USE_DEVELOP_BRANCH,
            'github' => FilamentPluginConfiguration::INITIALIZE_GITHUB,
            'gh-public' => FilamentPluginConfiguration::GITHUB_PUBLIC,
            'gh-description' => FilamentPluginConfiguration::GITHUB_DESCRIPTION,
            'gh-homepage' => FilamentPluginConfiguration::GITHUB_HOMEPAGE,
            'gh-org' => FilamentPluginConfiguration::GITHUB_ORGANIZATION,
            'pluginName' => FilamentPluginConfiguration::PLUGIN_NAME,
        ]);

        $shellConfiguration = new ShellConfiguration([
            'EDITOR' => FilamentPluginConfiguration::EDITOR,
        ]);

        (new SetConfig(
            $commandLineConfiguration,
            $shellConfiguration,
            $this->consoleWriter,
            $this->input
        ))([
            FilamentPluginConfiguration::COMMAND => self::class,
            FilamentPluginConfiguration::EDITOR => 'pstorm',
            FilamentPluginConfiguration::COMMIT_MESSAGE => 'Initial commit',
            FilamentPluginConfiguration::ROOT_PATH => getcwd(),
            FilamentPluginConfiguration::BROWSER => null,
            FilamentPluginConfiguration::FORCE_CREATE => false,
            FilamentPluginConfiguration::WITH_OUTPUT => false,
            FilamentPluginConfiguration::USE_DEVELOP_BRANCH => false,
            FilamentPluginConfiguration::INITIALIZE_GITHUB => false,
            FilamentPluginConfiguration::GITHUB_PUBLIC => false,
            FilamentPluginConfiguration::PLUGIN_NAME => null,
            FilamentPluginConfiguration::GITHUB_DESCRIPTION => null,
            FilamentPluginConfiguration::GITHUB_HOMEPAGE => null,
            FilamentPluginConfiguration::GITHUB_ORGANIZATION => null,
        ]);

        if ($this->consoleWriter->isDebug()) {
            $this->debugReport();
        }
    }
}
