#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use App\Console\Command\HashingCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new HashingCommand());

$application->run();