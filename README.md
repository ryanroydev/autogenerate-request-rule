# Autogenerate Request Rule

[![Latest Version](https://img.shields.io/github/release/ryanroydev/autogenerate-request-rule.svg?style=flat-square)](https://github.com/ryanroydev/autogenerate-request-rule/releases)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

A custom Laravel package that automatically generates request validation rules for your application.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)

## Installation

You can install the package via Composer. Run the following command in your Laravel application's root directory:

```bash
composer require ryanroydev/autogenerate-request-rule

```
## Available Commands

You can use the following Artisan command to generate request validation rules for a specified controller:

```bash
php artisan ryanroydev:autogenerate-request-rule ControllerClass

```
Replace ControllerClass with the name of your controller.

## Usage

```php
use App\Http\Requests\YourGeneratedRequest;

public function store(YourGeneratedRequest $request)
{
    // The incoming request is valid...
    // Your logic here
}
```

Once you have generated the request rules, you can use them in your controllers to validate incoming requests. Here's an example of how to apply the generated rules:

## License

MIT License

Copyright (c) 2024 ryanroydev

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

1. The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

2. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

