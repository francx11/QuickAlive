<?php

declare(strict_types=1);

$basePath = dirname(__DIR__, 2);

require $basePath . "/vendor/autoload.php";

(new \App\Kernel($basePath))->run();
