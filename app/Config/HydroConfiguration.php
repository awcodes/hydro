<?php

namespace App\Config;

use Illuminate\Support\Str;

abstract class HydroConfiguration
{
    public const TARGET = 'target';

    public const EDITOR = 'editor';

    public const PLUGIN_NAME = 'plugin_name';

    public const ROOT_PATH = 'root_path';

    public const WITH_OUTPUT = 'with_output';

    public const FORCE_CREATE = 'force_create';

    public const COMMIT_MESSAGE = 'commit_message';

    public const INITIALIZE_GITHUB = 'initialize_github';

    public const GITHUB_PUBLIC = 'github_public';

    public const GITHUB_HOMEPAGE = 'github_homepage';

    public const GITHUB_ORGANIZATION = 'github_organization';

    public const COMMAND = 'command';

    public const AUTHOR = 'author';

    public const EMAIL = 'email';

    public const USERNAME = 'username';

    public const VENDOR = 'vendor';

    public const VENDOR_SLUG = 'vendor_slug';

    public const VENDOR_NAMESPACE = 'vendor_namespace';

    public const DESCRIPTION = 'description';

    public const PHPSTAN = 'no_phpstan';

    public const PINT = 'no_pint';

    public const DEPENDABOT = 'no_dependabot';

    public const RAY = 'no_ray';

    public const CHANGELOG_WORKFLOW = 'no_changelog_workflow';

    public const THEME = 'theme';

    public const FOR_FORMS = 'for_forms';

    public const FOR_TABLES = 'for_tables';

    public function __construct(array $keyMap)
    {
        $settings = $this->getSettings();

        collect($keyMap)->each(function ($item, $key) use ($settings) {
            $this->$item = $this->get($key, $settings);
        });
    }

    abstract protected function getSettings(): array;

    protected function get(string $key, array $array): mixed
    {
        if (array_key_exists($key, $array)) {
            if ($array[$key] === '') {
                return null;
            }

            if (in_array(Str::lower($array[$key]), ['1', 'true', 'on', 'yes'])) {
                return true;
            }

            if (in_array(Str::lower($array[$key]), ['0', 'false', 'off', 'no'])) {
                return false;
            }

            return $array[$key];
        }

        return null;
    }

    public function __get($name)
    {
        return null;
    }
}
