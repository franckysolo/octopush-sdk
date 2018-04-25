# Octopush-sdk

[![Latest Stable Version](https://poser.pugx.org/franckysolo/octopush-sdk/version)](https://packagist.org/packages/franckysolo/octopush-sdk)
[![Total Downloads](https://poser.pugx.org/franckysolo/octopush-sdk/downloads)](https://packagist.org/packages/franckysolo/octopush-sdk)
[![Latest Unstable Version](https://poser.pugx.org/franckysolo/octopush-sdk/v/unstable)](//packagist.org/packages/franckysolo/octopush-sdk)
[![License](https://poser.pugx.org/franckysolo/octopush-sdk/license)](https://packagist.org/packages/franckysolo/octopush-sdk)
[![composer.lock available](https://poser.pugx.org/franckysolo/octopush-sdk/composerlock)](https://packagist.org/packages/franckysolo/octopush-sdk)

Php SDK for [Octopush](http://www.octopush.com/) API SMS

## Installation

```
composer require franckysolo/octopush-sdk
```

## tests

Create a env.php file in tests/config

```php
<?php

define('OCTOPUSH_API_KEY', 'your-key');
define('OCTOPUSH_LOGIN', 'your-login');

?>

```

To run tests

```
composer test
```

## Usages


### Get Credit

```php
<?php

require_once '../vendor/autoload.php';
require_once '../configs/app.php';

use Octopush\Api;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$credit = $api->getCredit();
?>
<pre>
Remaining Credit :  <?php echo $credit;?> &euro;
</pre>

```

### Get Balance

```php
<?php
require_once '../vendor/autoload.php';
require_once '../configs/app.php';

use Octopush\Api;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$low = $api->getBalance();
$premium = $api->getBalance(false);
?>
<pre>
  Remaining Sms Low cost :  <?php echo $low;?>

  Remaining Sms Premium :  <?php echo $premium;?>
</pre>

```

### Send a simple message

```php
<?php
require_once '../vendor/autoload.php';
require_once '../configs/app.php';

use Octopush\Api;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$message = 'this is a simple sms message';
$api->sendMessage($message, [
  'sms_recipients' => TEST_PHONE_NUMBER,
  'sms_text' => $message,
  'sms_type' => Message::SMS_PREMIUM,
  'sms_sender' => 'Octopush sdk'
]);
?>
<pre>
<?php echo var_dump($api->getClient()->getResponse());?>
</pre>

```

### Send a publipostage message

```php
<?php
require_once '../vendor/autoload.php';
require_once '../configs/app.php';

use Octopush\Api;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$message = 'Hello {ch1} {nom} {prenom}, your session begin at {ch2} the {ch3}';

$api->sendMessage($message, [
  'sms_recipients' => [TEST_PHONE_NUMBER, TEST_PHONE_NUMBER_ALT],
  'sms_text' => $message,
  'sms_type' => Message::SMS_PREMIUM,
  'sms_sender' => 'Octopush sdk',
  'request_mode' => Message::SIMULATION_MODE,
  'recipients_first_names' => ['John', 'Jane'],
  'recipients_last_names' => ['John', 'Jane'],
  'sms_fields_1' => ['Mr', 'Miss'],
  'sms_fields_2' => ['08:00 am', '01:00 pm'],
  'sms_fields_3' => ['2018/05/21', '2018/05/22'],
]);

```
