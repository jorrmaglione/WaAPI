<?php

use Jorrmaglione\Waapi\WaInstance;

include_once __DIR__ . '/../vendor/autoload.php';
try {
    $wApi = new Jorrmaglione\Waapi\WaClient('WzJs8wdeRHTwToIbY2elZJrK6E9UyZ9wWDUqBgRgbbdf7b93');
    $inst = new WaInstance($wApi, 84554);
} catch (Exception $e) {
    fprintf(STDOUT, "Error: %s\n", $e->getMessage());
}
