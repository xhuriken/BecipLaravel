<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cron_error.log');

$rootPath = '/home/beciptq/www/'; // Vérifie le chemin correct de Laravel

// Charge Laravel
require $rootPath . 'vendor/autoload.php';
$app = require_once $rootPath . 'bootstrap/app.php';

// Vérifie que l'application Laravel est bien chargée
if (!$app) {
    die("L'application Laravel n'a pas été chargée correctement.");
}

// Exécute les commandes Artisan
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('email:daily');
$kernel->call('email:daily_client');

echo "Emails envoyés avec succès !";
