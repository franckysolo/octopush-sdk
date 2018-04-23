<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once '../vendor/autoload.php';
require_once '../tests/config/env.php';

use Octopush\Api;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$low = $api->getBalance();
$premium = $api->getBalance(false);
?>
<pre>
  Remaining Sms Low cost :  <?php echo $low;?>
  
  Remaining Sms Premium :  <?php echo $premium;?>
</pre>
