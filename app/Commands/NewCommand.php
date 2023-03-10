<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayWelcome;
use App\Actions\InitializeGitHubRepository;
use App\Actions\InitializeGitRepository;
use App\Actions\InstallComposerDependencies;
use App\Actions\InstallNpmDependencies;
use App\Actions\OpenInEditor;
use App\Actions\ProcessPluginStubs;
use App\Actions\PushToGitHub;
use App\Actions\ValidateGitHubConfiguration;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\Actions\VerifyPluginDetails;
use App\Concerns\Debug;
use App\Config\CommandLineConfiguration;
use App\Config\HydroConfiguration;
use App\Config\SavedConfiguration;
use App\Config\SetConfig;
use App\Config\ShellConfiguration;
use App\ConsoleWriter;
use App\Options;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Termwind\{terminal};

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
                return $carry.$this->buildSignatureOption($option);
            },
            "new\n{pluginName? : Name of the Filament plugin}"
        );
    }

    public function buildSignatureOption($option): string
    {
        $commandlineOption = isset($option['short']) ? ($option['short'].'|'.$option['long']) : $option['long'];

        if (isset($option['param_description'])) {
            $commandlineOption .= '='.($option['default'] ?? '');
        }

        return "\n{--{$commandlineOption} : {$option['cli_description']}}";
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function handle(): int
    {
        terminal()->clear();

        app(DisplayWelcome::class)();

        if (! $this->argument('pluginName')) {
            app(DisplayHelpScreen::class)();
            exit;
        }

        $this->setConsoleWriter();
        $this->setConfig();

        try {
            app(VerifyDependencies::class)();
            app(VerifyPathAvailable::class)();
            app(VerifyPluginDetails::class)();
            app(ProcessPluginStubs::class)();
            app(InstallNpmDependencies::class)();
            app(InstallComposerDependencies::class)();
            app(InitializeGitRepository::class)();
            app(ValidateGitHubConfiguration::class)();
            app(InitializeGitHubRepository::class)();
            app(PushToGitHub::class)();
            app(OpenInEditor::class)();
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
        config(['hydro.store' => []]);

        $commandLineConfiguration = new CommandLineConfiguration([
            'editor' => HydroConfiguration::EDITOR,
            'message' => HydroConfiguration::COMMIT_MESSAGE,
            'path' => HydroConfiguration::ROOT_PATH,
            'force' => HydroConfiguration::FORCE_CREATE,
            'with-output' => HydroConfiguration::WITH_OUTPUT,
            'github' => HydroConfiguration::INITIALIZE_GITHUB,
            'gh-public' => HydroConfiguration::GITHUB_PUBLIC,
            'gh-homepage' => HydroConfiguration::GITHUB_HOMEPAGE,
            'gh-org' => HydroConfiguration::GITHUB_ORGANIZATION,
            'pluginName' => HydroConfiguration::PLUGIN_NAME,
            'target' => HydroConfiguration::TARGET,
            'author' => HydroConfiguration::AUTHOR,
            'email' => HydroConfiguration::EMAIL,
            'username' => HydroConfiguration::USERNAME,
            'vendor' => HydroConfiguration::VENDOR,
            'vendor-slug' => HydroConfiguration::VENDOR_SLUG,
            'vendor-namespace' => HydroConfiguration::VENDOR_NAMESPACE,
            'description' => HydroConfiguration::DESCRIPTION,
            'no-phpstan' => HydroConfiguration::PHPSTAN,
            'no-pint' => HydroConfiguration::PINT,
            'no-dependabot' => HydroConfiguration::DEPENDABOT,
            'no-ray' => HydroConfiguration::RAY,
            'no-changelog-workflow' => HydroConfiguration::CHANGELOG_WORKFLOW,
            'theme' => HydroConfiguration::THEME,
            'for-forms' => HydroConfiguration::FOR_FORMS,
            'for-tables' => HydroConfiguration::FOR_TABLES,
        ]);

        $savedConfiguration = new SavedConfiguration([
            'PROJECTPATH' => HydroConfiguration::ROOT_PATH,
            'MESSAGE' => HydroConfiguration::COMMIT_MESSAGE,
            'CODEEDITOR' => HydroConfiguration::EDITOR,
            'AUTHOR' => HydroConfiguration::AUTHOR,
            'EMAIL' => HydroConfiguration::EMAIL,
            'USERNAME' => HydroConfiguration::USERNAME,
            'FILAMENTVERSION' => HydroConfiguration::TARGET,
            'VENDOR' => HydroConfiguration::VENDOR,
            'VENDORSLUG' => HydroConfiguration::VENDOR_SLUG,
            'VENDORNAMESPACE' => HydroConfiguration::VENDOR_NAMESPACE,
        ]);

        $shellConfiguration = new ShellConfiguration([
            'EDITOR' => HydroConfiguration::EDITOR,
        ]);

        (new SetConfig(
            $commandLineConfiguration,
            $savedConfiguration,
            $shellConfiguration,
            $this->consoleWriter,
            $this->input
        ))([
            HydroConfiguration::COMMAND => self::class,
            HydroConfiguration::ROOT_PATH => getcwd(),
            HydroConfiguration::FORCE_CREATE => false,
            HydroConfiguration::WITH_OUTPUT => false,
            HydroConfiguration::TARGET => '2.x',
            HydroConfiguration::EDITOR => 'nano',
            HydroConfiguration::AUTHOR => null,
            HydroConfiguration::EMAIL => null,
            HydroConfiguration::USERNAME => null,
            HydroConfiguration::VENDOR => null,
            HydroConfiguration::VENDOR_SLUG => null,
            HydroConfiguration::VENDOR_NAMESPACE => null,
            HydroConfiguration::DESCRIPTION => null,
            HydroConfiguration::PHPSTAN => true,
            HydroConfiguration::PINT => true,
            HydroConfiguration::DEPENDABOT => true,
            HydroConfiguration::RAY => true,
            HydroConfiguration::CHANGELOG_WORKFLOW => true,
            HydroConfiguration::THEME => false,
            HydroConfiguration::FOR_FORMS => false,
            HydroConfiguration::FOR_TABLES => false,
            HydroConfiguration::INITIALIZE_GITHUB => false,
            HydroConfiguration::COMMIT_MESSAGE => 'Initial commit',
            HydroConfiguration::GITHUB_PUBLIC => false,
            HydroConfiguration::PLUGIN_NAME => null,
            HydroConfiguration::GITHUB_HOMEPAGE => null,
            HydroConfiguration::GITHUB_ORGANIZATION => null,
        ]);

        if ($this->consoleWriter->isDebug()) {
            $this->debugReport();
        }
    }
}
