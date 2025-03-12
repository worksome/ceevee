# Ceevee

[![Latest Version on Packagist](https://img.shields.io/packagist/v/worksome/ceevee.svg?style=flat-square)](https://packagist.org/packages/worksome/ceevee)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/worksome/ceevee/tests.yml?label=tests&style=flat-square)](https://github.com/worksome/ceevee/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/worksome/ceevee.svg?style=flat-square)](https://packagist.org/packages/worksome/ceevee)

Simple CV and Resume parsing for Laravel applications.

## Install

You can install the package via composer:

```bash
composer require worksome/ceevee
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ceevee-config"
```

This is the contents of the published config file:

```php
return [

    'default' => env('CEEVEE_DRIVER', 'null'),

    'services' => [

        'sovren' => [
            'account_id' => env('SOVREN_ACCOUNT_ID'),
            'service_key' => env('SOVREN_SERVICE_KEY'),
            'region' => 'eu',
            'options' => [],
        ],

    ],

];
```

## Usage

```php
$ceevee = new Worksome\Ceevee\Ceevee();

$details = $ceevee->read($file);

// Get the summary
$details->summary();
```

Check out [`Worksome\Ceevee\Support\CVDetail`](src/Support/CVDetail.php) for available options.

## Testing

```bash
composer test
```

## Changelog

Please see [GitHub Releases](https://github.com/worksome/ceevee/releases) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Luke Downing](https://github.com/worksome)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
