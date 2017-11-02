<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/Client.php');

use Mailman2Wrapper\Client;

if(!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])){
    line('Usage: composer test <host> <group> <password>');
    exit(0);
}

$HOST = $argv[1];
$GROUP = $argv[2];
$PASSWORD = $argv[3];

/* Test login */
try {
    $client = new Client($HOST, $GROUP, $PASSWORD);
}
catch(Exception $e){
    error('Unable to login');
    error('Code ' . $e->getCode() . ': ' . $e->getMessage());
    exit(0);
}

/* Test subscribe */
try {
    $client->subscribe("email@example.com", false, false);
}
catch(Exception $e){
    error('Unable to subscribe');
    error('Code ' . $e->getCode() . ': ' . $e->getMessage());
    exit(0);
}

/* Check if subscribed successfully */
try {
    $list = $client->listSubscribers();
    if(!in_array("email@example.com", $list)){
        error('Subscribed email not found in subscribers list!');
        exit(0);
    }
}
catch(Exception $e){
    error('Unable to list');
    error('Code ' . $e->getCode() . ': ' . $e->getMessage());
    exit(0);
}

/* Test unsubscribe */
try {
    $client->unsubscribe("email@example.com");
}
catch(Exception $e){
    error('Unable to unsubscribe');
    error('Code ' . $e->getCode() . ': ' . $e->getMessage());
    exit(0);
}

/* Check if unsubscribed successfully */
try {
    $list = $client->listSubscribers();
    if(in_array("email@example.com", $list)){
        error('Unubscribed email found in subscribers list!');
        exit(0);
    }
}
catch(Exception $e){
    error('Unable to list');
    error('Code ' . $e->getCode() . ': ' . $e->getMessage());
    exit(0);
}

/* Auxiliary functions */
function line($message){
    echo "$message\n";
}
function error($message){
    echo "[ERROR]: $message\n";
}
