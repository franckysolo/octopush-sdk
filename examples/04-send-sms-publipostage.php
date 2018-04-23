<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once '../vendor/autoload.php';
require_once '../tests/config/env.php';

use Octopush\Api;
use Octopush\Message;

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
?>

<pre>
<?php echo var_dump($api->getClient()->getResponse());?>
</pre>
