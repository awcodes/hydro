<?php

namespace App\Actions\Concerns;

use App\Config\HydroConfiguration;
use Exception;

trait InteractsWithGitHub
{
    protected static function shouldCreateRepository(): bool
    {
        return static::gitHubInitializationRequested() && static::gitHubToolingInstalled();
    }

    protected static function gitHubInitializationRequested(): bool
    {
        return config('hydro.store.'.HydroConfiguration::INITIALIZE_GITHUB) === true;
    }

    protected static function getDescription(): string
    {
        $description = config('hydro.store.'.HydroConfiguration::DESCRIPTION);

        if (is_null($description)) {
            return '';
        }

        return sprintf(' --description="%s"', $description);
    }

    protected static function getHomepage(): string
    {
        $homepage = config('hydro.store.'.HydroConfiguration::GITHUB_HOMEPAGE);

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
                config('hydro.store.github_public') ? ' --public' : ' --private',
                static::getDescription(),
                static::getHomepage(),
            );
        }

        if (static::hubInstalled()) {
            return sprintf(
                'hub create %s%s%s%s',
                config('hydro.store.github_public') ? '' : '--private ',
                static::getDescription(),
                static::getHomepage(),
                static::getRepositoryName()
            );
        }

        throw new Exception("Missing tool. Expected one of 'gh' or 'hub' to be installed but none found.");
    }

    protected static function getRepositoryName(): string
    {
        $name = config('hydro.store.project_name');
        $organization = config('hydro.store.'.HydroConfiguration::GITHUB_ORGANIZATION);

        return $organization ? " $organization/$name" : " $name";
    }

    protected static function ghInstalled(): bool
    {
        return config('hydro.store.tools.gh') === true;
    }

    protected static function hubInstalled(): bool
    {
        return config('hydro.store.tools.hub') === true;
    }

    protected static function gitHubToolingInstalled(): bool
    {
        return static::ghInstalled() || static::hubInstalled();
    }
}
