<?php

// Tests the impact of the 'select_timeout' param of the CurlMultiHandler.

// composer require guzzlehttp/guzzle
include (__DIR__ . '/../vendor/autoload.php');
include (__DIR__ . '/common.php');

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;

// Initialize a client
$handler = new CurlMultiHandler(['select_timeout' => 0.001]);
$client = new Client(['handler' => $handler]);
client($client);

function fetch_stub($id, $latency) {
    return Promise\coroutine(function () use ($id, $latency) {
        $request = new Request('GET', "http://httpbin.org/delay/$latency?id=$id");
        $response = yield client()->sendAsync($request);
        $result = json_decode($response->getBody(), true);
        yield $result['args'];
    });
}

function fetch_stub_repeated($iterations, $base_id, $latency) {
    return Promise\coroutine(function () use ($iterations, $base_id, $latency) {
        for ($i = 0; $i < $iterations; $i++) {
            $time = microtime(true);
            yield fetch_stub("$base_id--$i", $latency);
            $duration = (microtime(true) - $time) * 1000;
            println(sprintf('%s request finished : %f ms', $base_id, $duration));
        }
    });
}

Promise\all([
  fetch_stub_repeated(1000, 'short', 0),
  fetch_stub_repeated(100, 'long', 2),
])->wait();

