<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once '../vendor/autoload.php';
require_once '../tests/config/env.php';

use Octopush\Api;
use Octopush\Message;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$message = 'this is a simple message';
$api->sendMessage($message, [
  'sms_recipients' => TEST_PHONE_NUMBER,
  'sms_text' => $message,
  'sms_type' => Message::SMS_LOW_COST,
  'sms_sender' => 'Octopush sdk',
  'request_mode' => Message::SIMULATION_MODE
]);
?>
<pre>
<?php echo var_dump($api->getClient()->getResponse());?>
</pre>
