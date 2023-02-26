<?php

namespace App\Actions\Concerns;

use App\Config\FilamentPluginConfiguration;
use Exception;

trait InteractsWithGitHub
{
    protected static function shouldCreateRepository(): bool
    {
        return static::gitHubInitializationRequested() && static::gitHubToolingInstalled();
    }

    protected static function gitHubInitializationRequested(): bool
    {
        return config('filament-plugin.store.'.FilamentPluginConfiguration::INITIALIZE_GITHUB) === true;
    }

    protected static function getDescription(): string
    {
        $description = config('filament-plugin.store.'.FilamentPluginConfiguration::GITHUB_DESCRIPTION);

        if (is_null($description)) {
            return '';
        }

        return sprintf(' --description="%s"', $description);
    }

    protected static function getHomepage(): string
    {
        $homepage = config('filament-plugin.store.'.FilamentPluginConfiguration::GITHUB_HOMEPAGE);

        if (is_null($homepage)) {
            return '';
        }

        return sprintf(' --homepage="%s"', $homepage);
    }

    /**
     * @throws Exception
     */
    protected static function getGitHubCreateCommand(): string
    {
        if (static::ghInstalled()) {
            return sprintf(
                'gh repo create%s --confirm %s%s%s',
                static::getRepositoryName(),
                config('filament-plugin.store.github_public') ? ' --public' : ' --private',
                static::getDescription(),
                static::getHomepage(),
            );
        }

        if (static::hubInstalled()) {
            return sprintf(
                'hub create %s%s%s%s',
                config('filament-plugin.store.github_public') ? '' : '--private ',
                static::getDescription(),
                static::getHomepage(),
                static::getRepositoryName()
            );
        }

        throw new Exception("Missing tool. Expected one of 'gh' or 'hub' to be installed but none found.");
    }

    protected static function getRepositoryName(): string
    {
        $name = config('filament-plugin.store.project_name');
        $organization = config('filament-plugin.store.'.FilamentPluginConfiguration::GITHUB_ORGANIZATION);

        return $organization ? " $organization/$name" : " $name";
    }

    protected static function ghInstalled(): bool
    {
        return config('filament-plugin.store.tools.gh') === true;
    }

    protected static function hubInstalled(): bool
    {
        return config('filament-plugin.store.tools.hub') === true;
    }

    protected static function gitHubToolingInstalled(): bool
    {
        return static::ghInstalled() || static::hubInstalled();
    }
}
