<?php

namespace App;

class Options
{
    protected array $options = [
        [
            'long' => 'author-name',
            'param_description' => 'AUTHOR_NAME',
            'cli_description' => 'Author name',
        ],
        [
            'long' => 'author-email',
            'param_description' => 'AUTHOR_EMAIL',
            'cli_description' => 'Author email',
        ],
        [
            'long' => 'author-username',
            'param_description' => 'AUTHOR_USERNAME',
            'cli_description' => 'Author username',
        ],
        [
            'long' => 'vendor-name',
            'param_description' => 'VENDOR_NAME',
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
            'long' => 'package-name',
            'param_description' => 'PACKAGE_NAME',
            'cli_description' => 'Package name',
        ],
        [
            'long' => 'package-slug',
            'param_description' => 'PACKAGE_SLUG',
            'cli_description' => 'Package slug',
        ],
        [
            'long' => 'package-class-name',
            'param_description' => 'PACKAGE_CLASS_NAME',
            'cli_description' => 'Package class name',
        ],
        [
            'long' => 'package-description',
            'param_description' => 'PACKAGE_DESCRIPTION',
            'cli_description' => 'Package description',
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
            'long' => 'theme',
            'cli_description' => 'Is plugin a custom theme',
        ],
        [
            'long' => 'for-forms',
            'cli_description' => 'Is plugin only for the Forms package',
        ],
        [
            'long' => 'for-tables',
            'cli_description' => 'Is plugin only for the Tables package',
        ],
        [
            'short' => 't',
            'long' => 'target',
            'param_description' => 'TARGET',
            'cli_description' => 'Which version of Filament to target',
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
            'cli_description' => 'Customize the initial commit message (wrap with quotes!)',
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
            'long' => 'gh-description',
            'param_description' => 'DESCRIPTION',
            'cli_description' => 'Initialize the new GitHub repository with the provided <info>DESCRIPTION</info>',
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
