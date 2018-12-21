<?php

// Test for https://github.com/guzzle/promises/pull/98

// composer require guzzlehttp/guzzle
include (__DIR__ . '/../vendor/autoload.php');

use GuzzleHttp\Promise;

function catchRejection($promise) {
    return $promise->then(null, function () { return false; })->wait();
}

function noThrow() {
    return Promise\coroutine(function () {
        yield true;
    });
}
function throwAfter() {
    return Promise\coroutine(function () {
        yield true;
        throw new \Exception();
    });
}
function throwBefore() {
    return Promise\coroutine(function () {
        throw new \Exception();
        yield true;
    });
}

var_dump(catchRejection(noThrow()));      // --> OK : true
var_dump(catchRejection(throwAfter()));   // --> OK : false
var_dump(catchRejection(throwBefore()));  // --> KO : Uncaught Exception
