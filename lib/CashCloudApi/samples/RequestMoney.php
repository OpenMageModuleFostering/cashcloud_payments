<?php
// autoload
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'credentials.php';

use CashCloud\Api\Rest\Auth;
use CashCloud\Api\Rest\Client;

$api = new Client(new Auth(CASHCLOUD_USER, CASHCLOUD_PASSWORD, CASHCLOUD_DEVICEID), null, CASHCLOUD_SANDBOX);
$request = new \CashCloud\Api\Method\RequestMoney(new \CashCloud\Api\Rest\CurlRequest());
$request->setEmail("customer@cashcloud.com");
$request->setAmount(100);
$request->setCurrency(Client::CURRENCY_CCR);
$request->setReason(1354);
$request->setRemark("123");
print_r($request->perform($api));
