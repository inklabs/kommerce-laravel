# Zen Kommerce - Sample Laravel Application

[![Build Status](https://travis-ci.org/inklabs/kommerce-laravel.svg?branch=master)](https://travis-ci.org/inklabs/kommerce-laravel)
[![License](https://img.shields.io/packagist/l/inklabs/kommerce-laravel.svg)](https://github.com/inklabs/kommerce-laravel/blob/master/LICENSE.txt)

This is a sample application utilizing [Zen Kommerce Core](https://github.com/inklabs/kommerce-core)

Docs: [Github Pages](https://inklabs.github.io/kommerce-laravel)

## Initialize project

```
composer create-project
```
## Tests

### Acceptance Tests

`vendor/bin/codecept run -c vendor/inklabs/kommerce-templates/codeception.yml`

## Serve

```
php artisan serve
open http://localhost:8000
```

## EasyPost

You will need a test API key to get shipping rates. https://www.easypost.com/getting-started

Update .env with your EASYPOST-API-KEY

## Contributing

Thank you for considering contributing to the sample Laravel application! The contribution guide can be found in the [Core Project](https://github.com/inklabs/kommerce-core/blob/master/CONTRIBUTING.md).

### Working with Base Templates

[Prefer source to edit your vendors](https://moquet.net/blog/5-features-about-composer-php/#5.-prefer-source-to-edit-your-vendors)

```
rm -rf vendor/inklabs/kommerce-templates
composer update inklabs/kommerce-templates --prefer-source
```

### License

The MIT License (MIT)

Copyright (c) 2016 Jamie Isaacs <pdt256@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
