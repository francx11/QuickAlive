<?php

declare(strict_types=1);

use App\Kernel;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

(new Kernel(dirname(__DIR__)))->run();
