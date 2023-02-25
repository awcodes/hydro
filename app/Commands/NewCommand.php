<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayWelcome;
use App\Actions\ValidateGitHubConfiguration;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\Actions\VerifyPluginDetails;
use App\Config\CommandLineConfiguration;
use App\Config\FilamentPluginConfiguration;
use App\Config\SetConfig;
use App\Config\ShellConfiguration;
use App\ConsoleWriter;
use App\Options;
use Exception;
use Illuminate\Support\Str;
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

        try {
            app(VerifyDependencies::class)();
            app(ValidateGitHubConfiguration::class)();
            app(VerifyPathAvailable::class)();
            app(VerifyPluginDetails::class)();
            dd(config('filament-plugin.store'));
            // app(CopySkeletonToProject::class)();
        } catch (Exception $e) {
            $this->consoleWriter->exception($e->getMessage());
            return self::FAILURE;
        }

        $this->consoleWriter->newLine();
        $this->consoleWriter->success('🦒 New Filament plugin scaffolded! <em>Make something great.</em>', 'Success');
        $this->consoleWriter->newLine();

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
            'force' => FilamentPluginConfiguration::FORCE_CREATE,
            'with-output' => FilamentPluginConfiguration::WITH_OUTPUT,
            'dev' => FilamentPluginConfiguration::USE_DEVELOP_BRANCH,
            'github' => FilamentPluginConfiguration::INITIALIZE_GITHUB,
            'gh-public' => FilamentPluginConfiguration::GITHUB_PUBLIC,
            'gh-description' => FilamentPluginConfiguration::GITHUB_DESCRIPTION,
            'gh-homepage' => FilamentPluginConfiguration::GITHUB_HOMEPAGE,
            'gh-org' => FilamentPluginConfiguration::GITHUB_ORGANIZATION,
            'pluginName' => FilamentPluginConfiguration::PLUGIN_NAME,
            'target' => FilamentPluginConfiguration::TARGET,
            'author-name' => FilamentPluginConfiguration::AUTHOR_NAME,
            'author-email' => FilamentPluginConfiguration::AUTHOR_EMAIL,
            'author-username' => FilamentPluginConfiguration::AUTHOR_USERNAME,
            'vendor-name' => FilamentPluginConfiguration::VENDOR_NAME,
            'vendor-slug' => FilamentPluginConfiguration::VENDOR_SLUG,
            'vendor-namespace' => FilamentPluginConfiguration::VENDOR_NAMESPACE,
            'package-name' => FilamentPluginConfiguration::PACKAGE_NAME,
            'package-slug' => FilamentPluginConfiguration::PACKAGE_SLUG,
            'package-class-name' => FilamentPluginConfiguration::PACKAGE_CLASS_NAME,
            'package-description' => FilamentPluginConfiguration::PACKAGE_DESCRIPTION,
            'no-phpstan' => FilamentPluginConfiguration::PHPSTAN,
            'no-pint' => FilamentPluginConfiguration::PINT,
            'no-dependabot' => FilamentPluginConfiguration::DEPENDABOT,
            'no-ray' => FilamentPluginConfiguration::RAY,
            'no-changelog-workflow' => FilamentPluginConfiguration::CHANGELOG_WORKFLOW,
            'theme' => FilamentPluginConfiguration::THEME,
            'for-forms' => FilamentPluginConfiguration::FOR_FORMS,
            'for-tables' => FilamentPluginConfiguration::FOR_TABLES,
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
            FilamentPluginConfiguration::TARGET => '2.x',
            FilamentPluginConfiguration::EDITOR => 'pstorm',
            FilamentPluginConfiguration::COMMIT_MESSAGE => 'Initial commit',
            FilamentPluginConfiguration::ROOT_PATH => getcwd(),
            FilamentPluginConfiguration::FORCE_CREATE => false,
            FilamentPluginConfiguration::WITH_OUTPUT => false,
            FilamentPluginConfiguration::USE_DEVELOP_BRANCH => false,
            FilamentPluginConfiguration::INITIALIZE_GITHUB => false,
            FilamentPluginConfiguration::GITHUB_PUBLIC => false,
            FilamentPluginConfiguration::PLUGIN_NAME => null,
            FilamentPluginConfiguration::GITHUB_DESCRIPTION => null,
            FilamentPluginConfiguration::GITHUB_HOMEPAGE => null,
            FilamentPluginConfiguration::GITHUB_ORGANIZATION => null,
            FilamentPluginConfiguration::PHPSTAN => true,
            FilamentPluginConfiguration::PINT => true,
            FilamentPluginConfiguration::DEPENDABOT => true,
            FilamentPluginConfiguration::RAY => true,
            FilamentPluginConfiguration::CHANGELOG_WORKFLOW => true,
            FilamentPluginConfiguration::THEME => false,
            FilamentPluginConfiguration::FOR_FORMS => false,
            FilamentPluginConfiguration::FOR_TABLES => false,
            FilamentPluginConfiguration::AUTHOR_NAME => null,
            FilamentPluginConfiguration::AUTHOR_EMAIL => null,
            FilamentPluginConfiguration::AUTHOR_USERNAME => null,
            FilamentPluginConfiguration::VENDOR_NAME => null,
            FilamentPluginConfiguration::VENDOR_SLUG => null,
            FilamentPluginConfiguration::VENDOR_NAMESPACE => null,
            FilamentPluginConfiguration::PACKAGE_NAME => null,
            FilamentPluginConfiguration::PACKAGE_SLUG => null,
            FilamentPluginConfiguration::PACKAGE_CLASS_NAME => null,
            FilamentPluginConfiguration::PACKAGE_DESCRIPTION => null,
        ]);

        if ($this->consoleWriter->isDebug()) {
            $this->debugReport();
        }
    }
}
