<?php

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * @return \GuzzleHttp\Client
 */
function client(ClientInterface $injected_client = null) {
    static $client;
    $client = $client ?? $injected_client ?? new Client();
    return $client;
}

function println($string) {
    echo $string . PHP_EOL;
}
