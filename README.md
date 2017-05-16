# NFQ Academy Learning platform
[![Build Status](https://travis-ci.org/nfqakademija/streetwise-sophisticated-hackers.svg?branch=master)](https://travis-ci.org/nfqakademija/streetwise-sophisticated-hackers)
[![Symfony](https://img.shields.io/badge/Symfony-%203.x-green.svg "Supports Symfony 3.x")](https://symfony.com/)

## Install and run

```bash
$ git clone https://github.com/nfqakademija/streetwise-sophisticated-hackers.git
$ composer install
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:update --force
$ php bin/console doctrine:fixtures:load
$ php bin/console server:start
```

### Contributors

- [eleggua999](https://github.com/eleggua999)
- [darius72](https://github.com/darius72)
