<?php

require_once('../vendor/autoload.php');
require_once('../src/Client.php');

use Mailman2Wrapper\Client;

$PASSWORD = isset($argv[1]) ? $argv[1] : "";

new Client("https://mlists.ist.utl.pt/mailman", "groups.neiist.socios", $PASSWORD);