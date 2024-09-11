# CustomCaptcha for Laravel 5/6/7

[![Build Status](https://travis-ci.org/mewebstudio/customcaptcha.svg?branch=master)](https://travis-ci.org/mewebstudio/customcaptcha) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mewebstudio/customcaptcha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mewebstudio/customcaptcha/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mews/customcaptcha/v/stable.svg)](https://packagist.org/packages/mews/customcaptcha)
[![Latest Unstable Version](https://poser.pugx.org/mews/customcaptcha/v/unstable.svg)](https://packagist.org/packages/mews/customcaptcha)
[![License](https://poser.pugx.org/mews/customcaptcha/license.svg)](https://packagist.org/packages/mews/customcaptcha)
[![Total Downloads](https://poser.pugx.org/mews/customcaptcha/downloads.svg)](https://packagist.org/packages/mews/customcaptcha)

A simple [Laravel 5/6](http://www.laravel.com/) service provider for including the [CustomCaptcha for Laravel](https://github.com/mewebstudio/customcaptcha).

for Laravel 4 [CustomCaptcha for Laravel Laravel 4](https://github.com/mewebstudio/customcaptcha/tree/master-l4)

## Preview
![Preview](https://image.ibb.co/kZxMLm/image.png)

- [CustomCaptcha for Laravel 5/6/7](#customcaptcha-for-laravel-5-6-7)
  * [Preview](#preview)
  * [Installation](#installation)
  * [Usage](#usage)
  * [Configuration](#configuration)
  * [Example Usage](#example-usage)
    + [Session Mode:](#session-mode-)
    + [Stateless Mode:](#stateless-mode-)
- [Return Image](#return-image)
- [Return URL](#return-url)
- [Return HTML](#return-html)
- [To use different configurations](#to-use-different-configurations)
  * [Links](#links)
  
## Installation

The CustomCaptcha Service Provider can be installed via [Composer](http://getcomposer.org) by requiring the
`mews/customcaptcha` package and setting the `minimum-stability` to `dev` (required for Laravel 5) in your
project's `composer.json`.

```json
{
    "require": {
        "laravel/framework": "5.0.*",
        "mews/customcaptcha": "~2.0"
    },
    "minimum-stability": "dev"
}
```

or

Require this package with composer:
```
composer require mews/customcaptcha
```

Update your packages with ```composer update``` or install with ```composer install```.

In Windows, you'll need to include the GD2 DLL `php_gd2.dll` in php.ini. And you also need include `php_fileinfo.dll` and `php_mbstring.dll` to fit the requirements of `mews/customcaptcha`'s dependencies.




## Usage

To use the CustomCaptcha Service Provider, you must register the provider when bootstrapping your Laravel application. There are
essentially two ways to do this.

Find the `providers` key in `config/app.php` and register the CustomCaptcha Service Provider.

```php
    'providers' => [
        // ...
        'Impiger\CustomCaptcha\CustomCaptchaServiceProvider',
    ]
```
for Laravel 5.1+
```php
    'providers' => [
        // ...
        Impiger\CustomCaptcha\CustomCaptchaServiceProvider::class,
    ]
```

Find the `aliases` key in `config/app.php`.

```php
    'aliases' => [
        // ...
        'CustomCaptcha' => 'Impiger\CustomCaptcha\Facades\CustomCaptcha',
    ]
```
for Laravel 5.1+
```php
    'aliases' => [
        // ...
        'CustomCaptcha' => Impiger\CustomCaptcha\Facades\CustomCaptcha::class,
    ]
```

## Configuration

To use your own settings, publish config.

```$ php artisan vendor:publish```

`config/customcaptcha.php`

```php
return [
    'default'   => [
        'length'    => 5,
        'width'     => 120,
        'height'    => 36,
        'quality'   => 90,
        'math'      => true,  //Enable Math CustomCaptcha
        'expire'    => 60,    //Stateless/API customcaptcha expiration
    ],
    // ...
];
```

## Example Usage
### Session Mode:
```php

    // [your site path]/Http/routes.php
    Route::any('customcaptcha-test', function() {
        if (request()->getMethod() == 'POST') {
            $rules = ['customcaptcha' => 'required|customcaptcha'];
            $validator = validator()->make(request()->all(), $rules);
            if ($validator->fails()) {
                echo '<p style="color: #ff0000;">Incorrect!</p>';
            } else {
                echo '<p style="color: #00ff30;">Matched :)</p>';
            }
        }
    
        $form = '<form method="post" action="customcaptcha-test">';
        $form .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
        $form .= '<p>' . customcaptcha_img() . '</p>';
        $form .= '<p><input type="text" name="customcaptcha"></p>';
        $form .= '<p><button type="submit" name="check">Check</button></p>';
        $form .= '</form>';
        return $form;
    });
```
### Stateless Mode:
You get key and img from this url
`http://localhost/customcaptcha/api/math`
and verify the customcaptcha using this method:
```php
    //key is the one that you got from json response
    // fix validator
    // $rules = ['customcaptcha' => 'required|customcaptcha_api:'. request('key')];
    $rules = ['customcaptcha' => 'required|customcaptcha_api:'. request('key') . ',math'];
    $validator = validator()->make(request()->all(), $rules);
    if ($validator->fails()) {
        return response()->json([
            'message' => 'invalid customcaptcha',
        ]);

    } else {
        //do the job
    }
```

# Return Image
```php
customcaptcha();
```
or
```php
CustomCaptcha::create();
```


# Return URL
```php
customcaptcha_src();
```
or
```
CustomCaptcha::src('default');
```

# Return HTML
```php
customcaptcha_img();
```
or
```php
CustomCaptcha::img();
```

# To use different configurations
```php
customcaptcha_img('flat');

CustomCaptcha::img('inverse');
```
etc.

Based on [Intervention Image](https://github.com/Intervention/image)

^_^

## Links
* [Intervention Image](https://github.com/Intervention/image)
* [L5 CustomCaptcha on Github](https://github.com/mewebstudio/customcaptcha)
* [L5 CustomCaptcha on Packagist](https://packagist.org/packages/mews/customcaptcha)
* [For L4 on Github](https://github.com/mewebstudio/customcaptcha/tree/master-l4)
* [License](http://www.opensource.org/licenses/mit-license.php)
* [Laravel website](http://laravel.com)
* [Laravel Turkiye website](http://www.laravel.gen.tr)
* [MeWebStudio website](http://www.mewebstudio.com)
