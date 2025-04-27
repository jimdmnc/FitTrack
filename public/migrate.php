<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

Artisan::call('migrate', [
    '--force' => true, // Important: force running migrations in production
]);

echo "Migration completed!";
?>
