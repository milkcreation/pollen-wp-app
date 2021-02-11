# Pollen Wordpress App

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/wp-app/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen **Wordpress App** Component is a Micro Framework.
It contains a set of useful tools for developing applications in a Wordpress environment. 

## Installation

```bash
composer require pollen-solutions/wp-app
```

## Setup

```php
// wp-content/themes/{{ your-theme }}/functions.php
use Pollen\WpApp\WpApp;

// @see vendor/pollen-solutions/wp-app/resources/config/wp-app.php
new WpApp([/** Configuration args */]);
```
