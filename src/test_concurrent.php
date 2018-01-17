<?php

// composer require guzzlehttp/guzzle
include (__DIR__ . '/../vendor/autoload.php');
include ('./common.php');

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;

$time = 0;
$delay = 0;

function fetch_stub($id, $latency) {
    return Promise\coroutine(function () use ($id, $latency) {
        $request = new Request('GET', "http://httpbin.org/delay/$latency?id=$id");
        $response = yield client()->sendAsync($request);
        $result = json_decode($response->getBody(), true);
        yield $result['args'];
    });
}

$times = [];
Promise\all([

  Promise\coroutine(function () use (&$times) {
      for ($i = 0; $i < 10; $i++) {
          $time = microtime(true);
          yield fetch_stub('b' . $i, 0);
          $times[$i] = (microtime(true) - $time) * 1000;
      }
  }),

  Promise\coroutine(function () use (&$times) {
      for ($i = 0; $i < 4; $i++) {
          yield fetch_stub('c' . $i, 1);
      }
  }),

])->wait();

var_export($times);
