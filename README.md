# Laravel Database Translation from Coder's Cantina

A powerful, database-driven translation system for Laravel applications. Store, manage, and retrieve translations directly from your database, with full integration with Laravel's built-in translation system.

## Features

- **Database Storage**: Store all translations in your database for easy management
- **Laravel Integration**: Seamlessly integrates with Laravel's built-in translation system
- **Placeholder Support**: Full support for placeholders in translations
- **Fallback Support**: Cascading language fallbacks
- **Import/Export**: Import and export translations from/to JSON files
- **Command Line Tools**: Comprehensive set of Artisan commands to manage translations

## Getting started

* Install this package

## Installation

### Requirements

- PHP 8.0 or higher
- Laravel 8.0 or higher

### Via Composer

```bash
composer require coderscantina/translations
```

The package will automatically register its service provider.

### Publish Configuration

Optionally publish the configuration file:

```bash
php artisan vendor:publish --provider="CodersCantina\Translations\ServiceProvider" --tag="config"
```

### Run Migrations

Create the translations table:

```bash
php artisan migrate
```
## Usage

### Adding Translations

#### Via Artisan Command

```bash
# Basic usage
php artisan translations:add welcome.message 'Welcome to our application'

# With language option
php artisan translations:add welcome.message 'Willkommen in unserer Anwendung' --lang=de

# With namespace
php artisan translations:add welcome.message 'Welcome to our application' --namespace=frontend
```
#### Via Code

```php
use CodersCantina\Translations\Translation;

Translation::create([
    'key' => 'welcome.message',
    'value' => 'Welcome to our application',
    'language_iso' => 'en'
]);
```
### Retrieving Translations

Use Laravel's built-in methods for retrieving translations, such as `Lang::get()`, `__` and `trans` helpers.

```php
// Using the __ helper
echo __('welcome.message'); // 'Welcome to our application'

// With replacements
echo __('welcome.user', ['name' => 'John']); // 'Welcome, John'

// Specifying language
echo __('welcome.message', [], 'de'); // 'Willkommen in unserer Anwendung'

// Using the trans helper
echo trans('welcome.message');

// Using the facade
use Illuminate\Support\Facades\Lang;
echo Lang::get('welcome.message');
```

### Organizing Translations

We strongly recommend using dot notation to logically group translations:

```
auth.login.title
auth.login.email
auth.login.password
auth.register.title
errors.validation.required
errors.server.unavailable
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```
