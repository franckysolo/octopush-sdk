<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once '../vendor/autoload.php';
require_once '../tests/config/env.php';

use Octopush\Api;

$api = new Api(OCTOPUSH_LOGIN, OCTOPUSH_API_KEY);
$credit = $api->getCredit();
?>
<pre>
Remaining Credit :  <?php echo $credit;?> &euro;
</pre>
