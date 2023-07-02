# Laravel Cache Migration

The package is developed for make seamless deploys. You can delete your cache by adding the desired redis keys to the migration file.

## Requirement

- **Laravel** >= 5.x
- **PHP** >= 7.1

## Installation

Install via composer

```bash
composer require haydarsahin/cache-migration
```

Run migration for creating `cache-migrations` table.

```bash
php artisan migrate
```

## Usage

The cache migrations is analogous with normal migration.

Create a migration file.

```bash
php artisan make:cache-migration UsersCacheForget
```

Type pattern or patterns which you want to forget to patterns array.

```php
<?php

class UserCacheForget
{

   /*
   |--------------------------------------------------------------------------
   | Cache Migration File
   |--------------------------------------------------------------------------
   |
   | Redis keys that you wish to clear should be added to the patterns array.
   | Invalid patterns: '*', less than 3 characters.
   |
   */

    public $patterns = [
        'users:*',
        'report:users:*:performance:*:dateRange:*:volumes',
        'specificCacheKey:101:userId'
    ];
}

```

Run pending migrations manually or add this code to your deployment pipeline.

```bash
php artisan cache:migrate
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [All contributors](https://github.com/haydar/cache-migration/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
