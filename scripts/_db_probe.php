<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\\Contracts\\Console\\Kernel');
$kernel->bootstrap();

$db = Illuminate\Support\Facades\DB::getDatabaseName();
$rows = Illuminate\Support\Facades\DB::table('information_schema.COLUMNS')
    ->select('COLUMN_NAME','IS_NULLABLE','COLUMN_DEFAULT')
    ->where('TABLE_SCHEMA', $db)
    ->where('TABLE_NAME', 'item_master')
    ->where('COLUMN_NAME', 'service_id')
    ->get();
print_r($rows->all());
