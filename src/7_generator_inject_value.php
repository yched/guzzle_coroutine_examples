<?php

require ('./common.php');

function my_generator($count, $seed = 0) {
    for ($i = 0; $i <= $count; $i++) {
        try {
            $seed = yield rand($seed * 100, ($seed + 1) * 100);
            println("generator received $seed");
        }
        catch (\Exception $e) {
            println("generator received an exception");
            yield -1;
        }
    }
}

$it = my_generator(10);
$count = 0;
while ($it->valid()) {
    println("$count : generator yielded " . $it->current());
    if ($count !== 3) {
        $it->send(rand(1, 5));
    }
    else {
        $it->throw(new \RuntimeException('Bad luck!'));
    }
    $count++;
}
