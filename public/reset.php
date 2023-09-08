<?php

declare(strict_types=1);

$kernel = require_once(dirname(__DIR__) . '/app/bootstrap/loader.php');

$query = file_get_contents(APP_PATH_MIGRATIONS . '1_schema.sql') . PHP_EOL . file_get_contents(APP_PATH_MIGRATIONS . '2_filling.sql');

$kernel->db()->querySimple($query);

echo 'Stats reset complete!';
