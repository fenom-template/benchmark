<?php

use Fenom\Benchmark;

require(__DIR__.'/../vendor/autoload.php');

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
    "help"
));

$opt += array(
    "stress" => 0
);

extract($opt);



if(isset($h) || isset($help)) {
    echo "
Start: ".basename(__FILE__)." [--stress COUNT] [--auto_reload] [--cleanup] [--markdown]
Usage: ".basename(__FILE__)." [--help | -h]
";
    exit;
}

Benchmark::init();

Benchmark::$stress = intval($stress);
Benchmark::$auto_reload = isset($auto_reload);

exec("rm -rf ".__DIR__."/../compile/*");

echo "Smarty3 vs Twig vs Fenom\n\n";

echo "Generate templates... ";
passthru("php ".Benchmark::$tpl_path."/inheritance/smarty.gen.php");
passthru("php ".Benchmark::$tpl_path."/inheritance/twig.gen.php");
echo "Done\n";

echo "Testing a lot output...\n";

Benchmark::runs("smarty3", 'echo/smarty.tpl',   Benchmark::$tpl_path.'/echo/data.json');
Benchmark::runs("twig",    'echo/twig.tpl',     Benchmark::$tpl_path.'/echo/data.json');
Benchmark::runs("fenom",   'echo/smarty.tpl',   Benchmark::$tpl_path.'/echo/data.json');
//if(extension_loaded("phalcon")) {
//    Benchmark::runs("volt",   'echo/twig.tpl',  __DIR__.'/templates/echo/data.json');
//}
echo "\nTesting 'foreach' of big array...\n";

Benchmark::runs("smarty3", 'foreach/smarty.tpl', Benchmark::$tpl_path.'/foreach/data.json');
Benchmark::runs("twig",    'foreach/twig.tpl',   Benchmark::$tpl_path.'/foreach/data.json');
Benchmark::runs("fenom",   'foreach/smarty.tpl', Benchmark::$tpl_path.'/foreach/data.json');
//if(extension_loaded("phalcon")) {
//    Benchmark::runs("volt",   'foreach/twig.tpl', __DIR__.'/templates/foreach/data.json');
//}

echo "\nTesting deep 'inheritance'...\n";

Benchmark::runs("smarty3", 'inheritance/smarty/b100.tpl', Benchmark::$tpl_path.'/foreach/data.json');
Benchmark::runs("twig",    'inheritance/twig/b100.tpl', Benchmark::$tpl_path.'/foreach/data.json');
Benchmark::runs("fenom",  'inheritance/smarty/b100.tpl', Benchmark::$tpl_path.'/foreach/data.json');
//if(extension_loaded("phalcon")) {
//    Benchmark::runs("volt",  'inheritance/twig/b100.tpl', __DIR__.'/templates/foreach/data.json');
//}

echo "\nDone\n";
if(isset($cleanup)) {
    echo "Cleanup.\n";
    passthru("rm -rf ".__DIR__."/../compile/*");
    passthru("rm -f ".__DIR__."/../templates/inheritance/smarty/*");
    passthru("rm -f ".__DIR__."/../templates/inheritance/twig/*");
}
