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
    ){
        $this->authorName = config('filament-plugin.store.author_name');
        $this->authorEmail = config('filament-plugin.store.author_email');
        $this->authorUsername = config('filament-plugin.store.author_username');
        $this->vendorName = config('filament-plugin.store.vendor_name');
        $this->vendorSlug = config('filament-plugin.store.vendor_slug');
        $this->vendorNamespace = config('filament-plugin.store.vendor_namespace');
        $this->packageName = config('filament-plugin.store.package_name');
        $this->packageSlug = config('filament-plugin.store.package_slug');
        $this->packageClassName = config('filament-plugin.store.package_class_name');
        $this->packageDescription = config('filament-plugin.store.package_description');
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        if (! $this->authorName) {
            $gitName = exec('git config user.name');
            $this->authorName = $this->consoleWriter->ask('Author name', $gitName);
            config()->set(
                'filament-plugin.store.author_name',
                $this->authorName
            );
        }

        if (! $this->authorEmail) {
            $gitEmail = exec('git config user.email');
            $this->authorEmail = $this->consoleWriter->ask('Author email', $gitEmail);
            config()->set(
                'filament-plugin.store.author_email',
                $this->authorEmail
            );
        }

        if (! $this->authorUsername) {
            $gitUsername = exec('git config user.username');
            $this->authorUsername = $this->consoleWriter->ask('Author username', $gitUsername);
            config()->set(
                'filament-plugin.store.author_username',
                $this->authorUsername
            );
        }

        if (! $this->vendorName) {
            $this->vendorName = $this->consoleWriter->ask('Vender name', $this->authorUsername);
            config()->set(
                'filament-plugin.store.vendor_name',
                $this->vendorName
            );
        }

        if (! $this->vendorSlug) {
            $this->vendorSlug = Str::of($this->vendorName)->slug()->replace('/[^A-Za-z0-9-]+/', '-')->rtrim('-');
            config()->set(
                'filament-plugin.store.vendor_slug',
                $this->vendorSlug
            );
        }

        if (! $this->vendorNamespace) {
            $this->vendorNamespace = $this->consoleWriter->ask('Vendor namespace', ucwords($this->vendorName));
            config()->set(
                'filament-plugin.store.vendor_namespace',
                $this->vendorNamespace
            );
        }

        if (! $this->packageName) {
            $this->packageName = $this->consoleWriter->ask('Package name', ucwords(config('filament-plugin.store.plugin_name')));
            config()->set(
                'filament-plugin.store.package_name',
                $this->packageName
            );
        }

        if (! $this->packageSlug) {
            $this->packageSlug = Str::of($this->packageName)->slug();
            config()->set(
                'filament-plugin.store.package_slug',
                $this->packageSlug
            );
        }

        if (! $this->packageClassName) {
            $this->packageClassName = $this->consoleWriter->ask('Class name', Str::of($this->packageName)->title());
            config()->set(
                'filament-plugin.store.package_class_name',
                $this->packageClassName
            );
        }

        if (! $this->packageDescription) {
            $this->packageDescription = $this->consoleWriter->ask('Package description', "This is my package $this->packageSlug");
            config()->set(
                'filament-plugin.store.package_description',
                $this->packageDescription
            );
        }

        //
//        $this->line("\r");
//        $this->line('------');
//        $this->line("Author     : \e[0;36m$this->authorName ($this->authorUsername, $this->authorEmail)\e[0m");
//        $this->line("Vendor     : \e[0;36m$this->vendorName ($this->vendorSlug)\e[0m");
//        $this->line('Package    : '."\e[0;36m".$this->packageSlug.($this->packageDescription ? " ($this->packageDescription)" : '')."\e[0m");
//        $this->line("Namespace  : \e[0;36m$this->vendorNamespace\\$this->packageClassName\e[0m");
//        $this->line("Class name : \e[0;36m$this->packageClassName\e[0m");
//        $this->line('---');
//        $this->line("\e[1;37mPackages & Utilities\e[0m");
//        $this->line('Larastan/PhpStan  : '.($this->usePhpStan ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
//        $this->line('Pint              : '.($this->usePint ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
//        $this->line('Use Dependabot    : '.($this->useDependabot ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
//        $this->line('Use Laravel Ray   : '.($this->useLaravelRay ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
//        $this->line('Auto-Changelog    : '.($this->useChangelogWorkflow ? "\e[0;32mYes" : "\e[0;31mNo")."\e[0m");
//        if ($this->forFormsOnly) {
//            $this->line("Filament/Forms    : \e[0;32mYes\e[0m");
//        } elseif ($this->forTablesOnly) {
//            $this->line("Filament/Tables   : \e[0;32mYes\e[0m");
//        } else {
//            $this->line("Filament/Filament : \e[0;32mYes\e[0m");
//        }
//        $this->line('------');
//        $this->line("\r");
//        $this->line('This script will replace the above values in all relevant files in the plugin directory.');
//        $this->line("\r");
//
        if (! $this->consoleWriter->confirm('Looks good to me!')) {
            $this->consoleWriter->exception('😥 Too bad. I bet it would\'ve been a good one.');
            exit;
        }
    }
}
