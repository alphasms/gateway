# AlphaSMS API gateway

Send SMS and Viber message via AlphaSMS ([alphasms.ua](https://alphasms.ua/)) using this framework package.

Library for sending and checking of the statuses SMS and Viber messages via the AlphaSMS.ua service.


#### PHP >= 5.6.0
Minimum PHP interpreter version is `5.6`


## Installation
Install the package via composer.

``` bash
composer require alphasms/gateway
```

## How to get an API key

- Register in the website [https://alphasms.ua/](https://alphasms.ua)
- Sign in into personal cabinet [https://alphasms.ua/panel/](https://alphasms.ua/panel/)
- Visit a settings section [https://alphasms.ua/panel/settings/](https://alphasms.ua/panel/settings/)
- Open a tab `API` and find you own `API key` 

### Usage in Yii2

Put to `component` section in you config file a next fragment:

``` php
    'alphasms' => [
        'class' => 'alphasms\gateway\AlphaSMS',
        'config' => [
            'api_key' => '',
            'sms_sender' => 'Your alphaname',
        ],
    ],
```

Here you can fill all of parameters `api_key`, `sms_sender`, `viber_sender`, etc.

You always can access an AlphaSMS gateway like in example bellow:

```php
Yii::$app->alphasms::sendSms([...]);
```

## Usage in Laravel and in other frameworks

In other cases you should define a used class and call a static method:

```php
<?php

use \alphasms\gateway\AlphaSMS;

class SiteController extends Controller
{
    public function actionIndex()
    {
        AlphaSMS::setApiKey('...');
        AlphaSMS::setSmsSender('Alphaname');
        
        AlphaSMS::sendSms(['...']);
    }
}
```

### Configuration methods

`setApiKey($apiKey)` - `string` specify an API key different from the value in the config
```php
AlphaSMS::setApiKey('API_KEY');
```

`setSMSSender($smsSender)` - `string` specify an SMS sender different from the value in the config
```php
AlphaSMS::setSmsSender('AlphaSMS');
```

`setViberSender($viberSender)` - `string` specify a Viber sender different from the value in the config
```php
AlphaSMS::setViberSender('ViberSender');
```

`setViberLifetime($seconds)` - `int` lifetime of Viber message in seconds (60 - 86400)
```php
AlphaSMS::setViberLifetime(60);
```

`setViberForceSms($int)` - `int` re-send a message via SMS if it is impossible to deliver via Viber
```php
AlphaSMS::setViberForceSms(1);
```

### Price method

`getPrice($array)` - `array` tries to know  a price of SMS sending by an array of data in first parameter. Returns `string` error or `array` with `float` price and `string` currency.
```php
$price_arr = AlphaSMS::getPrice([
    'recipient' => '380961234567',        // string
    'message' => 'SMS text goes here',    // string
]);
```

### Sending methods

`sendSms($array)` - `array` sends simple SMS by an array of data in first parameter. Returns `string` error or `int` with queue Id. This Id can be used for check a status or decline a sending.
```php
$queue_or_error = AlphaSMS::sendSms([
    // required
    'recipient' => '380961234567',        // string
    'message' => 'SMS text goes here',    // string
    
    // not required
    'sms_sender' => 'Alphaname',              // string
    'date_time' => '2025-01-01T09:00:00+0300', // string
]);
```

`sendViber($array)` - `array` sends a message to Viber by an array of data in first parameter. Returns `string` error or `int` with queue Id.
```php
$queue_or_error = AlphaSMS::sendViber([
    // required
    'recipient' => '380961234567',        // string
    'message' => 'Text of viber message', // string
    
    // not required
    'viber_sender' => 'Viber-name',             // string
    'date_time' => '2025-01-01T09:00:00+0300', // string
    'lifetime' => 60,                     // int|string
    'image' => 'full_path_to_image',      // string
    'button' => 'Visit a website',        // string
    'url' => 'http://site.com/',          // string
    
    'force_sms' => 1,                     // int|string
    'sms_sender' => 'Alpha-name',         // string
]);
```

### Other methods

`getBalance()` - returns `float` value of a balance by defined API key 

```php
$balance = AlphaSMS::getBalance();
```

`getStatus($queue_id)` - returns `string` with a status of message by queue Id `int`

```php
$status = AlphaSMS::getStatus(1234567);
```

`deleteQueue($queue_id)` - `int`. Tries to remove a delayed message (if was set `date_time` parameter). Returns `bool` true|false. 

```php
AlphaSMS::deleteQueue(1234567);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
