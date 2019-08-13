<?php
// autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'credentials.php';

use CashCloud\Api\Rest\Auth;
use CashCloud\Api\Rest\Client;

$api = new Client(new Auth(CASHCLOUD_USER, CASHCLOUD_PASSWORD, CASHCLOUD_DEVICEID), null, CASHCLOUD_SANDBOX);
$request = new \CashCloud\Api\Method\GetSettings();
print_r($request->perform($api));
