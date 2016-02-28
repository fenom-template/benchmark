<?php

use Colors\Color;
use Fenom\Benchmark;

require __DIR__ . '/../vendor/autoload.php';

$opt = getopt("h", array(
    /** @var string $message */
    "cleanup",
    /** @var boolean $stress */
    "stress:",
    /** @var boolean $auto_reload */
    "auto_reload",
    /** @vat boolean $markdown */
    "markdown",
    /** @vat boolean $help */
    /** @vat boolean $h */
    "help",
));

$opt += array(
    "stress" => 0,
);

extract($opt);

$c = new Color();

$c->setTheme(
    array(
        'header' => array('red'),
        'title' => array('magenta'),
        'done' => 'green',
    )
);

if (isset($h) || isset($help)) {
    echo "
Start: " . basename(__FILE__) . " [--stress COUNT] [--auto_reload] [--cleanup] [--markdown]
Usage: " . basename(__FILE__) . " [--help | -h]
";
    exit;
}

echo "\033c";

Benchmark::init();

Benchmark::$stress = intval($stress);
Benchmark::$auto_reload = isset($auto_reload);

exec("rm -rf " . __DIR__ . "/../compile/*");

echo $c('Smarty3 vs Twig vs Fenom')->header->bold . PHP_EOL . PHP_EOL;

echo $c('Generate templates...')->title->bold . PHP_EOL;
passthru("php " . Benchmark::$tpl_path . "/inheritance/smarty.gen.php");
passthru("php " . Benchmark::$tpl_path . "/inheritance/twig.gen.php");
echo $c('Done')->done . PHP_EOL . PHP_EOL;

echo $c('Testing a lot output...')->title->bold . PHP_EOL;

Benchmark::runs("smarty3", 'echo/smarty.tpl', Benchmark::$tpl_path . '/echo/data.json');
Benchmark::runs("twig", 'echo/twig.tpl', Benchmark::$tpl_path . '/echo/data.json');
Benchmark::runs("fenom", 'echo/smarty.tpl', Benchmark::$tpl_path . '/echo/data.json');

echo $c('Testing "foreach" of big array...')->title->bold . PHP_EOL;

Benchmark::runs("smarty3", 'foreach/smarty.tpl', Benchmark::$tpl_path . '/foreach/data.json');
Benchmark::runs("twig", 'foreach/twig.tpl', Benchmark::$tpl_path . '/foreach/data.json');
Benchmark::runs("fenom", 'foreach/smarty.tpl', Benchmark::$tpl_path . '/foreach/data.json');

echo $c("Testing deep 'inheritance'...")->title->bold . PHP_EOL;

Benchmark::runs("smarty3", 'inheritance/smarty/b100.tpl', Benchmark::$tpl_path . '/foreach/data.json');
Benchmark::runs("twig", 'inheritance/twig/b100.tpl', Benchmark::$tpl_path . '/foreach/data.json');
Benchmark::runs("fenom", 'inheritance/smarty/b100.tpl', Benchmark::$tpl_path . '/foreach/data.json');

echo $c('Done')->done . PHP_EOL . PHP_EOL;

if (isset($cleanup)) {
    echo "Cleanup.\n";
    passthru("rm -rf " . __DIR__ . "/../compile/*");
    passthru("rm -f " . __DIR__ . "/../templates/inheritance/smarty/*");
    passthru("rm -f " . __DIR__ . "/../templates/inheritance/twig/*");
}
