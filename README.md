# Phig

[![Coverage Status](https://coveralls.io/repos/github/marella/phig/badge.svg?branch=master)](https://coveralls.io/github/marella/phig?branch=master)
[![Build Status](https://travis-ci.org/marella/phig.svg?branch=master)](https://travis-ci.org/marella/phig)
[![StyleCI](https://styleci.io/repos/53027491/shield?style=flat)](https://styleci.io/repos/53027491)
[![Latest Stable Version](https://poser.pugx.org/marella/phig/v/stable)](https://packagist.org/packages/marella/phig) [![Total Downloads](https://poser.pugx.org/marella/phig/downloads)](https://packagist.org/packages/marella/phig) [![Latest Unstable Version](https://poser.pugx.org/marella/phig/v/unstable)](https://packagist.org/packages/marella/phig) [![License](https://poser.pugx.org/marella/phig/license)](https://packagist.org/packages/marella/phig)

PHP Config Library.


### Quick Usage

```sh
composer require marella/phig
```

```php
<?php

require 'vendor/autoload.php';

$loader = new \Phig\ConfigLoader();
$config = $loader->read(__DIR__.'/config');

$timezone = $config['app.timezone']; // 'UTC'
$locale = $config['app']['locale']; // 'en'
$database = $config['database']; // array
$host = $database['host']; // 'localhost'
```

where `config` directory contains these files:

`app.php`
```php
<?php

return [
    'timezone' => 'UTC',
    'locale' => 'en',
];
```

`database.php`
```php
<?php

return [
    'host' => 'localhost',
    'database' => 'forge',
    'username' => 'forge',
    'password' => '',
];
```

### Documentation
See the **[wiki]** for more details and documentation.

### Contributing
See [contributing guidelines] before creating issues or pull requests.

### License
Open-source software released under [the MIT license][license].

[wiki]: https://github.com/marella/phig/wiki
[contributing guidelines]: /.github/CONTRIBUTING.md
[license]: /LICENSE
