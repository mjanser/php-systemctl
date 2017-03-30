# SystemCtl for PHP

[![Build Status](https://travis-ci.org/mjanser/php-systemctl.svg?branch=master)](https://travis-ci.org/mjanser/php-systemctl)
[![Code Coverage](https://scrutinizer-ci.com/g/mjanser/php-systemctl/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mjanser/php-systemctl/?branch=master)

This library can be used to manage systemd services.
Internally it uses the [Symfony Process Component](https://symfony.com/doc/current/components/process.html) for running the `systemctl` command.

## Requirements

- PHP 7.0 or higher
- `systemd` installed

## Installation

Run the following composer command in your project:

```bash
composer require mjanser/systemctl
```

## Usage

Example usage:

```php
$service = new SystemCtl\Service('my-service');

if ($service->isRunning()) {
    $service->stop();
} else {
    $service->start();
}

$service->restart();
```

By default the command `systemctl` will be executed with `sudo`. You can change that if you need.

```php
SystemCtl\Service::setCommand('my-systemctl');
SystemCtl\Service::sudo(false);
```
