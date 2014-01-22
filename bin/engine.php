<?php

$opt = getopt("", array(
    /** @var string $engine */
    "engine:",
    /** @var string $template */
    "template:",
    /** @var string $data */
    "data:",
    /** @var boolean $double */
    "double",
    /** @var string $message */
    "message:",
    /** @var boolean $stress */
    "stress:",
    /** @var boolean $auto_reload */
    "auto_reload"
));


$opt += array(
    "message"     => "plain",
    "stress"      => 0,
);

extract($opt);

require(__DIR__.'/../vendor/autoload.php');

Fenom\Benchmark::init();

$time = Fenom\Benchmark::$engine($template, json_decode(file_get_contents($data), true), isset($double), $stress, isset($auto_reload));

printf(Fenom\Benchmark::OUTPUT, $engine, $message, round($time, 4), round(memory_get_peak_usage()/1024/1024, 2));