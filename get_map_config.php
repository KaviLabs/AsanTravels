<?php
/**
 * get_map_config.php
 * Serves the CARTO API key to the frontend via JSON.
 * This file reads from config.php which is gitignored — the key never enters the repo.
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

echo json_encode([
    'cartoKey' => defined('CARTO_API_KEY') ? CARTO_API_KEY : ''
]);
