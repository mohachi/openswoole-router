<?php

use League\CLImate\CLImate;
use NunoMaduro\Collision\Provider;

require_once __DIR__ . "/vendor/autoload.php";

(new Provider)->register();

$subtitutions = [
    "~(/\\\\\*(\\\\\*)+)+~" => "(/.+?)?",
    "~\\\\\*~" => "[^/]+?",
    "~\\\\{([^/]+?)\\\\}~" => "(?<$1>[^/]+?)"
];

$patterns = [
    "/",
    "/one",
    "/one/",
    "/one/two",
    "/one/two/",
    "/one/two/three",
    "/one.ext",
    "/one.ext/",
    "/one.ext/two.ext",
    "/one.ext/two.ext/",
    "/one.ext/two.ext/three.ext",
    "/*",
    "/*/one",
    "/*/one/two",
    "/**",
    "/***",
    "/**/*",
    "/**/**",
    "/**/one",
    "/**/one/two/",
    "/**/*/one/two/",
    "/*/**/*/first",
    "/manga/{manga}/chapter/{chapter}",
];

foreach( $patterns as $pattern )
{
    $quoted = preg_quote($pattern, "~");
    $replaced = preg_replace(array_keys($subtitutions), array_values($subtitutions), $quoted);
    
    (new CLImate)
    ->blue("ori: $pattern")
    ->yellow("quo: $quoted")
    ->green("rep: ~^$replaced$~")
    ->br();
}
