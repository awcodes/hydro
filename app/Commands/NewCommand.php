<?php

namespace App\Commands;

use App\Actions\ValidateGitHubConfiguration;
use App\Actions\VerifyDependencies;
use App\Actions\VerifyPathAvailable;
use App\ConsoleWriter;
use App\InstallerException;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    protected $signature = 'new {name : Name of the plugin}';

    protected $description = 'Scaffold a new Filament plugin.';

    /**
     * @throws InstallerException
     */
    public function handle(): int
    {
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

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $io = new OutputStyle($input, $output);

        app()->singleton(ConsoleWriter::class, function () use ($input, $output) {
            return new ConsoleWriter($input, $output);
        });

        app()->alias(ConsoleWriter::class, 'console-writer');

        return parent::run($input, $output);
    }
}
