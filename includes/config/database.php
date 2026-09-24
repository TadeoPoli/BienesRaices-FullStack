<?php

function conectarDB() : mysqli {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');

    if ($host === false || $port === false || $database === false || $username === false || $password === false) {
        throw new RuntimeException('Falta la configuración de la base de datos. Creá el archivo .env a partir de .env.example.');
    }

    $db = new mysqli($host, $username, $password, $database, (int) $port);

    if ($db->connect_errno) {
        throw new RuntimeException('No se pudo conectar a la base de datos.');
    }

    $db->set_charset('utf8mb4');

    return $db;
}
