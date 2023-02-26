# This is my package curator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/curator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/awcodes/curator/run-tests?label=tests)](https://github.com/awcodes/curator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/awcodes/curator/Check%20&%20fix%20styling?label=code%20style)](https://github.com/awcodes/curator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/curator)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require awcodes/curator
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="curator-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="curator-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="curator-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$curator = new Awcodes\Curator();
echo $curator->echoPhrase('Hello, Awcodes!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
