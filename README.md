# Autogenerate Request Rule

![Total Downloads](https://img.shields.io/packagist/dt/ryanroydev/autogenerate-request-rule.svg?style=flat-square)
[![Latest Version](https://img.shields.io/github/release/ryanroydev/autogenerate-request-rule.svg?style=flat-square)](https://github.com/ryanroydev/autogenerate-request-rule/releases)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

A custom Laravel package that automatically generates request validation rules for your application.



## Table of Contents

- [Installation](#installation)
- [Available Commands](#available-commands)
- [Usage](#usage)
- [Required Inputs in Form View](#required-inputs-in-form-view)
- [Returning Views in Controller](#returning-views-in-controller)
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

## Input and Output 
![Input Image](https://ryanroydev.github.io/autogenerate-request-rule/input.PNG)

![Output Image](https://ryanroydev.github.io/autogenerate-request-rule/output.PNG)

## Required Inputs in Form View

When creating your form views, Ensure that the input has a name attribute and type attribute.

```php
<input type="text" name="firstname" >
```

## Returning Views in Controller

Please ensure that your selected controller class includes a return view in order for the command to detect it:

```php
public function create()
{
    return view('your_view_name'); // Replace 'your_view_name' with the actual view file name
}
```

## Usage

Once you have generated the request rules, you can use them in your controllers to validate incoming requests. Here's an example of how to apply the generated rules:


```php
use App\Http\Requests\YourGeneratedRequest;

public function store(YourGeneratedRequest $request)
{
    // The incoming request is valid...
    // Your logic here
}
```

## License

MIT License

Copyright (c) 2024 ryanroydev

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

1. The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

2. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

