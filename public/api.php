<?php

$endpoint = $_GET['endpoint'] ?? null;
$acceptsHtml = strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false;
define('XGP_ROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

switch ($endpoint) {
    case 'players':
        $data = include XGP_ROOT . '/public/api/PlayerData.php';  
        break;
    case 'universe':
        $data = include XGP_ROOT . '/public/api/ServerData.php';  
        break;
        //Add other endpoints as needed
    default:
        header("HTTP/1.0 404 Not Found");
        $data = ['error' => 'Endpoint not found'];
        break;
}