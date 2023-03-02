<?php

namespace App\Actions;

use App\Actions\Concerns\AbortsCommands;
use App\ConsoleWriter;
use App\Environment;
use App\Shell;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProcessPluginStubs
{
    use AbortsCommands;

    protected array $files;

    protected string $projectPath;

    protected string $authorName;

    protected string $authorEmail;

    protected string $authorUsername;

    protected string $vendorName;

    protected string $vendorSlug;

    protected string $vendorNamespace;

    protected string $packageName;

    protected string $packageSlug;

    protected string $packageClassName;

    protected string $packageDescription;

    protected string $target;

    public function __construct(
        protected ConsoleWriter $consoleWriter,
        protected Shell $shell,
    ) {
        $this->projectPath = config('hydro.store.project_path');
        $this->authorName = config('hydro.store.author');
        $this->authorEmail = config('hydro.store.email');
        $this->authorUsername = config('hydro.store.username');
        $this->vendorName = config('hydro.store.vendor');
        $this->vendorSlug = config('hydro.store.vendor_slug');
        $this->vendorNamespace = config('hydro.store.vendor_namespace');
        $this->packageName = config('hydro.store.plugin_name');
        $this->packageSlug = config('hydro.store.plugin_slug');
        $this->packageClassName = config('hydro.store.plugin_classname');
        $this->packageDescription = config('hydro.store.description') ?? '';
        $this->target = config('hydro.store.target') ?? '2.0';
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->consoleWriter->logStep('Scaffolding plugin...');

        $this->copyStubs();

        $this->files = Environment::isWin() ? $this->replaceForWindows() : $this->replaceForAllOtherOSes();

        $this->abortIf(! $this->files, 'Could not process stubs.');

        $this->files = collect($this->files)->transform(fn ($file) => $this->projectPath.Str::of($file)->ltrim('.'))->toArray();

        foreach ($this->files as $file) {
            $this->replaceInFile($file, [
                ':author_name' => $this->authorName,
                ':author_username' => $this->authorUsername,
                'author@domain.com' => $this->authorEmail,
                ':vendor_name' => $this->vendorName,
                ':vendor_slug' => $this->vendorSlug,
                'VendorName' => $this->vendorNamespace,
                ':package_name' => $this->packageName,
                ':package_slug' => $this->packageSlug,
                ':package_slug_without_prefix' => Str::of($this->packageSlug)->replace('laravel-', ''),
                'Skeleton' => $this->packageClassName,
                'skeleton' => $this->packageSlug,
                ':package_description' => $this->packageDescription,
                ':target' => Str::replace('x', '0', $this->target),
            ]);

            match (true) {
                str_contains($file, $this->determineSeparator('src/Skeleton.php')) => File::move($file, Str::of($file)->replace('Skeleton', $this->packageClassName)),
                str_contains($file, $this->determineSeparator('src/SkeletonTheme.php')) => File::move($file, Str::of($file)->replace('SkeletonTheme', $this->packageClassName)),
                str_contains($file, $this->determineSeparator('src/SkeletonServiceProvider.php')) => File::move($file, Str::of($file)->replace('Skeleton', $this->packageClassName)),
                str_contains($file, $this->determineSeparator('src/Facades/Skeleton.php')) => File::move($file, Str::of($file)->replace('Skeleton', $this->packageClassName)),
                str_contains($file, $this->determineSeparator('src/Testing/TestsSkeleton.php')) => File::move($file, Str::of($file)->replace('Skeleton', $this->packageClassName)),
                str_contains($file, $this->determineSeparator('src/Commands/SkeletonCommand.php')) => File::move($file, Str::of($file)->replace('Skeleton', $this->packageClassName)),
                str_contains($file, $this->determineSeparator('database/migrations/create_skeleton_table.php.stub')) => File::move($file, Str::of($file)->replace('skeleton', $this->packageSlug)),
                str_contains($file, $this->determineSeparator('config/skeleton.php')) => File::move($file, Str::of($file)->replace('skeleton', $this->packageSlug)),
                default => [],
            };
        }

        if (config('hydro.store.no_phpstan')) {
            File::delete($this->projectPath.'/phpstan.neon.dist');
            File::delete($this->projectPath.'/phpstan-baseline.neon');
            File::delete($this->projectPath.'/.github/workflows/phpstan.yml');

            $this->removeComposerDeps([
                'phpstan/extension-installer',
                'phpstan/phpstan-deprecation-rules',
                'phpstan/phpstan-phpunit',
                'nunomaduro/larastan',
            ]);

            $this->removeComposerScripts([
                'test:phpstan',
                '@test:phpstan',
            ]);
        }

        if (config('hydro.store.no_pint')) {
            File::delete($this->projectPath.'/.github/workflows/fix-php-code-style-issues.yml');

            $this->removeComposerDeps([
                'laravel/pint',
            ]);

            $this->removeComposerScripts([
                'pint',
            ]);
        }

        if (config('hydro.store.no_changelog_workflow')) {
            File::delete($this->projectPath.'/.github/workflows/update-changelog.yml');
            File::delete($this->projectPath.'/CHANGELOG.md');
        }

        if (config('hydro.store.no_ray')) {
            $this->removeComposerDeps([
                'spatie/laravel-ray',
            ]);
        }

        if (config('hydro.store.no_dependabot')) {
            File::delete($this->projectPath.'/.github/workflows/dependabot-auto-merge.yml');
            File::delete($this->projectPath.'/.github/dependabot.yml');
        }

        $this->consoleWriter->success('Plugin successfully scaffolded.');
        $this->consoleWriter->newLine();
    }

    private function copyStubs(): void
    {
        File::makeDirectory($this->projectPath);
        File::copyDirectory(__DIR__.'/../../stubs/common', $this->projectPath);
        File::copyDirectory(__DIR__.'/../../stubs/'.$this->target, $this->projectPath.'/src');

        if (config('hydro.store.theme')) {
            File::copy(__DIR__.'/../../stubs/'.$this->target.'/SkeletonTheme.php', $this->projectPath.'/src/SkeletonTheme.php');
            File::copy(__DIR__.'/../../stubs/configure/theme/package.json', $this->projectPath.'/package.json');
            File::copy(__DIR__.'/../../stubs/configure/theme/plugin.css', $this->projectPath.'/resources/css/plugin.css');
            File::copy(__DIR__.'/../../stubs/configure/theme/tailwind.config.js', $this->projectPath.'/tailwind.config.js');
            File::delete($this->projectPath.'/src/Skeleton.php');
            File::delete($this->projectPath.'/src/SkeletonServiceProvider.php');
            File::deleteDirectory($this->projectPath.'/config');
            File::deleteDirectory($this->projectPath.'/database');
            File::deleteDirectory($this->projectPath.'/stubs');
            File::deleteDirectory($this->projectPath.'/resources/js');
            File::deleteDirectory($this->projectPath.'/resources/lang');
            File::deleteDirectory($this->projectPath.'/resources/views');
            File::deleteDirectory($this->projectPath.'/src/Commands');
            File::deleteDirectory($this->projectPath.'/src/Facades');
        } else {
            File::copy(__DIR__.'/../../stubs/'.$this->target.'/SkeletonServiceProvider.php', $this->projectPath.'/src/SkeletonServiceProvider.php');
            File::copy(__DIR__.'/../../stubs/configure/package/package.json', $this->projectPath.'/package.json');
            File::copy(__DIR__.'/../../stubs/configure/package/plugin.css', $this->projectPath.'/resources/css/plugin.css');
            File::copy(__DIR__.'/../../stubs/configure/package/tailwind.config.js', $this->projectPath.'/tailwind.config.js');
        }

        if (config('hydro.store.for_forms')) {
            $this->removeComposerDeps([
                'filament/filament',
                'filament/tables',
            ]);
        } elseif (config('hydro.store.for_tables')) {
            $this->removeComposerDeps([
                'filament/filament',
                'filament/forms',
            ]);
        } else {
            $this->removeComposerDeps([
                'filament/forms',
                'filament/tables',
            ]);
        }

        if (config('hydro.store.theme')) {
            $this->cleanComposerForTheme();
            $this->removeComposerDeps([
                'illuminate/contracts',
                'spatie/laravel-package-tools',
            ]);
        }
    }

    private function replaceForWindows(): array
    {
        return array_filter(preg_split('/\\r\\n|\\r|\\n/', $this->shell->execQuietlyInProject('dir /S /B * | findstr /v /i .git\ | findstr /v /i vendor | findstr /v /i '.$this->projectPath.' | findstr /r /i /M /F:/ ":author :vendor :package VendorName skeleton vendor_name vendor_slug author@domain.com"')->getOutput()));
    }

    private function replaceForAllOtherOSes(): array
    {
        return array_filter(explode(PHP_EOL, $this->shell->execQuietlyInProject('grep -E -r -l -i ":author|:vendor|:package|VendorName|skeleton|vendor_name|vendor_slug|author@domain.com" --exclude-dir=vendor ./* ./.github/* | grep -v '.$this->projectPath)->getOutput()));
    }

    private function replaceInFile(string $file, array $replacements): void
    {
        $contents = file_get_contents($file);

        file_put_contents(
            $file,
            str_replace(
                array_keys($replacements),
                array_values($replacements),
                $contents
            )
        );
    }

    private function determineSeparator(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    private function removeComposerDeps(array $names): void
    {
        $data = json_decode(file_get_contents($this->projectPath.'/composer.json'), true);

        foreach ($data['require'] as $name => $version) {
            if (in_array($name, $names, true)) {
                unset($data['require'][$name]);
            }
        }

        foreach ($data['require-dev'] as $name => $version) {
            if (in_array($name, $names, true)) {
                unset($data['require-dev'][$name]);
            }
        }

        file_put_contents($this->projectPath.'/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function removeComposerScripts(array $scriptNames): void
    {
        $data = json_decode(file_get_contents($this->projectPath.'/composer.json'), true);

        foreach ($data['scripts'] as $name => $script) {
            if (is_array($script)) {
                foreach ($script as $k => $s) {
                    if (in_array($s, $scriptNames)) {
                        unset($data['scripts'][$name][$k]);

                        break;
                    }
                }
            } elseif (in_array($name, $scriptNames)) {
                unset($data['scripts'][$name]);

                break;
            }
        }

        file_put_contents($this->projectPath.'/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function cleanComposerForTheme(): void
    {
        $data = json_decode(file_get_contents($this->projectPath.'/composer.json'), true);

        unset($data['extra']);
        unset($data['autoload-dev']);
        unset($data['autoload']['psr-4']['VendorName\\Skeleton\\Database\\Factories\\']);

        file_put_contents($this->projectPath.'/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
