# Laravel Database Translation from Coder's Cantina

Database driven translations for your Laravel application

## Features

* Store translations in database
* Integrates with Laravel's built-in translation system and helpers

## Getting started

* Install this package

## Install

Require this package with composer:

``` bash
$ composer require coderscantina/translations
```

### Migrate the database

To add the translations table execute:

```bash
php artisan migrate
```

## Usage

Console command to add translations:

```bash
php artisan translations:add foo1 'bar baz'
php artisan translations:add foo2 'bar baz {quz}'
```

> We strongly advice to use a dot notation to logically group translations, like: `errors.payments.declined`

Use Laravel's built-in methods for retrieving translations, such as `Lang::get()`, `__` and `trans` helpers.

```php
__('foo1'); // bar baz
__('foo2', ['quz' => 'qux']); // bar baz qux
```
## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```
