<?php

require_once('../vendor/autoload.php');
require_once('../src/Client.php');

use Mailman2Wrapper\Client;

if(!isset($argv[1] || !isset($argv[2]) || !isset($argv[3]))){
    die("Usage: test.php host group password");
}
$HOST = $argv[1];
$GROUP = $argv[2];
$PASSWORD = $argv[2];

$client = new Client($HOST, $GROUP, $PASSWORD);