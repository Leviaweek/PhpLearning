<?php
return [
    'host' => getenv('DB_HOST'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'database' => getenv('DB_NAME'),
    'port' => 3306,
    'charset' => 'utf8mb4'
];