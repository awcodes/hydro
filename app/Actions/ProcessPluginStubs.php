<?php

namespace App\Actions;

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

    public function __construct(
        protected ConsoleWriter $consoleWriter,
        protected Shell $shell,
    ) {
        $this->projectPath = config('filament-plugin.store.project_path');
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->consoleWriter->logStep('Scaffolding plugin...');

        if (config('filament-plugin.store.target') === '3.x') {
            if (config('filament-plugin.store.for_forms')) {
                File::delete($this->projectPath.'/src/SkeletonTheme.php');

                $this->removeComposerDeps([
                    'filament/filament',
                    'filament/tables',
                ]);
            } elseif (config('filament-plugin.store.for_tables')) {
                File::delete($this->projectPath.'/src/SkeletonTheme.php');

                $this->removeComposerDeps([
                    'filament/filament',
                    'filament/forms',
                ]);
            } else {
                if (config('filament-plugin.store.theme')) {
                    File::delete($this->projectPath.'/src/SkeletonServiceProvider.php');
                    File::delete($this->projectPath.'/src/Skeleton.php');
                    File::deleteDirectory($this->projectPath.'/config');
                    File::deleteDirectory($this->projectPath.'/database');
                    File::deleteDirectory($this->projectPath.'/stubs');
                    File::deleteDirectory($this->projectPath.'/resources/js');
                    File::deleteDirectory($this->projectPath.'/resources/lang');
                    File::deleteDirectory($this->projectPath.'/resources/views');
                    File::deleteDirectory($this->projectPath.'/src/Commands');
                    File::deleteDirectory($this->projectPath.'/src/Facades');
                } else {
                    File::delete($this->projectPath.'/src/SkeletonTheme.php');
                }

                $this->removeComposerDeps([
                    'filament/forms',
                    'filament/tables',
                ]);
            }

            if (config('filament-plugin.store.theme')) {
                File::copy($this->projectPath.'/configure-stubs/theme/package.json', $this->projectPath.'/package.json');
                File::copy($this->projectPath.'/configure-stubs/theme/plugin.css', $this->projectPath.'/resources/css/plugin.css');
                File::copy($this->projectPath.'/configure-stubs/theme/tailwind.config.js', $this->projectPath.'/tailwind.config.js');
            } else {
                File::copy($this->projectPath.'/configure-stubs/package/package.json', $this->projectPath.'/package.json');
                File::copy($this->projectPath.'/configure-stubs/package/plugin.css', $this->projectPath.'/resources/css/plugin.css');
                File::copy($this->projectPath.'/configure-stubs/package/tailwind.config.js', $this->projectPath.'/tailwind.config.js');
            }

            File::deleteDirectory($this->projectPath.'/configure-stubs');
        }

        $this->files = Environment::isWin() ? $this->replaceForWindows() : $this->replaceForAllOtherOSes();

        $this->abortIf(! $this->files, 'Could not process stubs.');

        $this->files = collect($this->files)->transform(fn ($file) => $this->projectPath.Str::of($file)->ltrim('.'))->toArray();

        foreach ($this->files as $file) {
            $this->replaceInFile($file, [
                ':author_name' => config('filament-plugin.store.author_name'),
                ':author_username' => config('filament-plugin.store.author_username'),
                'author@domain.com' => config('filament-plugin.store.author_email'),
                ':vendor_name' => config('filament-plugin.store.vendor_name'),
                ':vendor_slug' => config('filament-plugin.store.vendor_slug'),
                'VendorName' => config('filament-plugin.store.vendor_namespace'),
                ':package_name' => config('filament-plugin.store.package_name'),
                ':package_slug' => config('filament-plugin.store.package_slug'),
                ':package_slug_without_prefix' => Str::of(config('filament-plugin.store.package_slug'))->replace('laravel-', ''),
                'Skeleton' => config('filament-plugin.store.package_class_name'),
                'skeleton' => config('filament-plugin.store.package_slug'),
                ':package_description' => config('filament-plugin.store.package_description'),
            ]);

            match (true) {
                str_contains(
                    $file,
                    $this->determineSeparator('src/Skeleton.php')) => File::move($file, Str::of($file)->replace('Skeleton', config('filament-plugin.store.package_class_name'))
                    ),
                str_contains(
                    $file,
                    $this->determineSeparator('src/SkeletonTheme.php')) => File::move($file, Str::of($file)->replace('Skeleton', config('filament-plugin.store.package_class_name'))
                    ),
                str_contains(
                    $file,
                    $this->determineSeparator('src/SkeletonServiceProvider.php')) => File::move($file, Str::of($file)->replace('Skeleton', config('filament-plugin.store.package_class_name'))
                    ),
                str_contains(
                    $file,
                    $this->determineSeparator('src/Facades/Skeleton.php')) => File::move($file, Str::of($file)->replace('Skeleton', config('filament-plugin.store.package_class_name'))
                    ),
                str_contains(
                    $file,
                    $this->determineSeparator('src/Testing/TestsSkeleton.php')) => File::move($file, Str::of($file)->replace('Skeleton', config('filament-plugin.store.package_class_name'))
                    ),
                str_contains(
                    $file,
                    $this->determineSeparator('src/Commands/SkeletonCommand.php')) => File::move($file, Str::of($file)->replace('Skeleton', config('filament-plugin.store.package_class_name'))
                    ),
                str_contains($file,
                    $this->determineSeparator('database/migrations/create_skeleton_table.php.stub')) => File::move($file, Str::of($file)->replace('skeleton', config('filament-plugin.store.package_slug'))
                    ),
                str_contains(
                    $file,
                    $this->determineSeparator('config/skeleton.php')) => File::move($file, Str::of($file)->replace('skeleton', config('filament-plugin.store.package_slug'))
                    ),
                str_contains($file, 'README.md') => $this->removeReadmeParagraphs($file),
                default => [],
            };
        }

        if (config('filament-plugin.store.no_phpstan')) {
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

        if (config('filament-plugin.store.no_pint')) {
            File::delete($this->projectPath.'/.github/workflows/fix-php-code-style-issues.yml');

            $this->removeComposerDeps([
                'laravel/pint',
            ]);

            $this->removeComposerScripts([
                'pint',
            ]);
        }

        if (config('filament-plugin.store.no_changelog_workflow')) {
            File::delete($this->projectPath.'/.github/workflows/update-changelog.yml');
            File::delete($this->projectPath.'/CHANGELOG.md');
        }

        if (config('filament-plugin.store.no_ray')) {
            $this->removeComposerDeps([
                'spatie/laravel-ray',
            ]);
        }

        if (config('filament-plugin.store.no_dependabot')) {
            File::delete($this->projectPath.'/.github/workflows/dependabot-auto-merge.yml');
            File::delete($this->projectPath.'/.github/dependabot.yml');
        }

        $this->consoleWriter->success('Plugin successfully scaffolded.');
        $this->consoleWriter->newLine();
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

    private function removeReadmeParagraphs(string $file): void
    {
        $contents = file_get_contents($file);

        file_put_contents(
            $file,
            preg_replace('/<!--delete-->.*<!--\/delete-->/s', '', $contents) ?: $contents
        );
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
}
