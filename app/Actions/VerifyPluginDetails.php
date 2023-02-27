<?php

namespace App\Actions;

use App\ConsoleWriter;
use Exception;
use Illuminate\Support\Str;

class VerifyPluginDetails
{
    use AbortsCommands;

    protected ?string $authorName;

    protected ?string $authorEmail;

    protected ?string $authorUsername;

    protected ?string $vendorName;

    protected ?string $vendorSlug;

    protected ?string $vendorNamespace;

    protected ?string $packageName;

    protected ?string $packageSlug;

    protected ?string $packageClassName;

    protected ?string $packageDescription;

    public function __construct(
        protected ConsoleWriter $consoleWriter
    ) {
        $this->authorName = config('hydro.store.author');
        $this->authorEmail = config('hydro.store.email');
        $this->authorUsername = config('hydro.store.username');
        $this->vendorName = config('hydro.store.vendor');
        $this->vendorSlug = config('hydro.store.vendor_slug');
        $this->vendorNamespace = config('hydro.store.vendor_namespace');
        $this->packageName = null;
        $this->packageSlug = null;
        $this->packageClassName = null;
        $this->packageDescription = null;
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->consoleWriter->logStep('Verifying plugin details...');

        $this->consoleWriter->newLine();

        $this->abortIf(
            config('hydro.store.target') === '2.x' && (config('hydro.store.for_forms') || config('hydro.store.for_tables') || config('hydro.store.theme')),
            "'--for_forms', '--for_tables' and '--theme' are only available for Filament 3.x"
        );

        if (! $this->authorName) {
            $gitName = exec('git config user.name');
            $this->authorName = $this->consoleWriter->ask('Author name', $gitName);
            config()->set('hydro.store.author', $this->authorName);
        }

        if (! $this->authorEmail) {
            $gitEmail = exec('git config user.email');
            $this->authorEmail = $this->consoleWriter->ask('Author email', $gitEmail);
            config()->set('hydro.store.email', $this->authorEmail);
        }

        if (! $this->authorUsername) {
            $this->authorUsername = $this->consoleWriter->ask('Author username (GitHub)', Str::of($this->authorName)->slug());
            config()->set('hydro.store.username', $this->authorUsername);
        }

        if (! $this->vendorName) {
            $this->vendorName = $this->consoleWriter->ask('Vendor name', Str::of($this->authorName)->slug());
            config()->set('hydro.store.vendor', $this->vendorName);
        }

        if (! $this->vendorSlug) {
            $this->vendorSlug = $this->consoleWriter->ask('Vendor slug', $this->guessSlug($this->vendorName));
            config()->set('hydro.store.vendor_slug', $this->vendorSlug);
        }

        if (! $this->vendorNamespace) {
            $this->vendorNamespace = $this->consoleWriter->ask('Vendor namespace', ucwords($this->vendorSlug));
            config()->set('hydro.store.namespace', $this->vendorNamespace);
        }

        $this->packageName = $this->consoleWriter->ask('Package name', config('hydro.store.plugin_name'));
        config()->set('hydro.store.package', $this->packageName);

        $this->packageSlug = $this->consoleWriter->ask('Package slug', config('hydro.store.plugin_slug'));
        config()->set('hydro.store.package_slug', $this->packageSlug);

        $this->packageClassName = $this->consoleWriter->ask('Class name', config('hydro.store.plugin_classname'));
        config()->set('hydro.store.classname', $this->packageClassName);

        $this->packageDescription = $this->consoleWriter->ask('Package description', "This is my package $this->packageSlug");
        config()->set('hydro.store.description', $this->packageDescription);

        $this->consoleWriter->text('--------');
        $this->consoleWriter->text("Author      : \e[0;36m$this->authorName ($this->authorEmail)\e[0m");
        $this->consoleWriter->text("Vendor      : \e[0;36m$this->vendorName ($this->vendorSlug)\e[0m");
        $this->consoleWriter->text("Package     : \e[0;36m$this->packageName ($this->packageSlug)\e[0m");
        $this->consoleWriter->text("Description : \e[0;36m".$this->packageDescription."\e[0m");
        $this->consoleWriter->text("Namespace   : \e[0;36m$this->vendorNamespace\\$this->packageClassName\e[0m");
        $this->consoleWriter->text("Class name  : \e[0;36m$this->packageClassName\e[0m");
        $this->consoleWriter->text('--------');
        $this->consoleWriter->text("\e[1;37mPackages & Utilities\e[0m");
        $this->consoleWriter->text('Larastan/PhpStan  : '.(! config('hydro.store.no_phpstan') ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
        $this->consoleWriter->text('Pint              : '.(! config('hydro.store.no_pint') ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
        $this->consoleWriter->text('Use Dependabot    : '.(! config('hydro.store.no_dependabot') ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
        $this->consoleWriter->text('Use Laravel Ray   : '.(! config('hydro.store.no_ray') ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
        $this->consoleWriter->text('Auto-Changelog    : '.(! config('hydro.store.no_changelog_workflow') ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");

        if (config('hydro.store.for_forms')) {
            $this->consoleWriter->text("Filament/Forms    : \e[0;32mYes\e[0m");
        } elseif (config('hydro.store.for_tables')) {
            $this->consoleWriter->text("Filament/Tables   : \e[0;32mYes\e[0m");
        } else {
            $this->consoleWriter->text("Filament/Filament : \e[0;32mYes\e[0m");
        }

        $this->consoleWriter->text('--------');
        $this->consoleWriter->newLine();

        if (! $this->consoleWriter->confirm('Looks good to me!')) {
            $this->consoleWriter->exception('😥 Too bad. I bet it would\'ve been a good one.');
            exit;
        }
    }

    private function guessSlug(string $name): string
    {
        return Str::of($name)->slug()->replace('/[^A-Za-z0-9-]+/', '-')->rtrim('-');
    }
}
