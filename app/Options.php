<?php

namespace App;

class Options
{
    protected array $options = [
        [
            'long' => 'author',
            'param_description' => 'AUTHOR',
            'cli_description' => 'Author name <fg=yellow>(wrap with quotes)</>',
        ],
        [
            'long' => 'email',
            'param_description' => 'EMAIL',
            'cli_description' => 'Author email',
        ],
        [
            'long' => 'username',
            'param_description' => 'USERNAME',
            'cli_description' => 'Author GitHub user name',
        ],
        [
            'long' => 'vendor',
            'param_description' => 'VENDOR',
            'cli_description' => 'Vendor name',
        ],
        [
            'long' => 'vendor-slug',
            'param_description' => 'VENDOR_SLUG',
            'cli_description' => 'Vendor slug',
        ],
        [
            'long' => 'vendor-namespace',
            'param_description' => 'VENDOR_NAMESPACE',
            'cli_description' => 'Vendor namespace',
        ],
        [
            'long' => 'description',
            'param_description' => 'DESCRIPTION',
            'cli_description' => 'Package description <fg=yellow>(wrap with quotes)</>',
        ],
        [
            'long' => 'no-phpstan',
            'cli_description' => 'Disable PhpStan',
        ],
        [
            'long' => 'no-pint',
            'cli_description' => 'Disable Pint',
        ],
        [
            'long' => 'no-dependabot',
            'cli_description' => 'Disable Dependabot',
        ],
        [
            'long' => 'no-ray',
            'cli_description' => 'Disable Laravel Ray for debugging',
        ],
        [
            'long' => 'no-changelog-workflow',
            'cli_description' => 'Disable automatic changelog updater workflow',
        ],
        [
            'short' => 't',
            'long' => 'target',
            'param_description' => 'TARGET',
            'cli_description' => 'Which version of Filament to target',
        ],
        [
            'long' => 'theme',
            'cli_description' => 'Is plugin a custom theme <fg=yellow>(3.x only)</>',
        ],
        [
            'long' => 'for-forms',
            'cli_description' => 'Is plugin only for the Forms package <fg=yellow>(3.x only)</>',
        ],
        [
            'long' => 'for-tables',
            'cli_description' => 'Is plugin only for the Tables package <fg=yellow>(3.x only)</>',
        ],
        [
            'short' => 'e',
            'long' => 'editor',
            'param_description' => 'EDITOR',
            'cli_description' => "Specify an editor to run <info>'EDITOR .'</info> with after",
        ],
        [
            'short' => 'p',
            'long' => 'path',
            'param_description' => 'PATH',
            'cli_description' => 'Customize the path in which the new project will be created',
        ],
        [
            'short' => 'm',
            'long' => 'message',
            'param_description' => 'MESSAGE',
            'cli_description' => 'Customize the initial commit message <fg=yellow>(wrap with quotes)</>',
        ],
        [
            'short' => 'g',
            'long' => 'github',
            'cli_description' => 'Initialize a new private GitHub repository',
        ],
        [
            'long' => 'gh-public',
            'cli_description' => 'Make the new GitHub repository public',
        ],
        [
            'long' => 'gh-homepage',
            'param_description' => 'URL',
            'cli_description' => 'Initialize the new GitHub repository with the provided homepage <info>URL</info>',
        ],
        [
            'long' => 'gh-org',
            'param_description' => 'ORG',
            'cli_description' => 'Initialize the new GitHub repository for <info>ORG</info>/project',
        ],
        [
            'short' => 'f',
            'long' => 'force',
            'cli_description' => 'Force install even if the directory already exists',
        ],
        [
            'short' => 'q',
            'long' => 'quiet',
            'cli_description' => 'Do not output to the console (except for user input)',
        ],
    ];

    public function all(): array
    {
        return $this->options;
    }
}
