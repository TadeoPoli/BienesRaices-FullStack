<?php

declare(strict_types=1);

/*
 * Router para el servidor integrado de PHP.
 * Ejecutar desde la raíz del proyecto:
 * php -S 127.0.0.1:8000 -t public router.php
 */

$publicRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'public');

if ($publicRoot === false) {
    http_response_code(500);
    exit('No se encontró el directorio público.');
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestPath = rawurldecode($requestPath);

// No se sirven archivos ocultos, internos ni rutas que intenten salir de public/.
if (
    str_contains($requestPath, "\0") ||
    preg_match('#(?:^|[\\\\/])\\.\\.(?:[\\\\/]|$)#', $requestPath) ||
    preg_match('#(?:^|/)\\.[^/]+$#', $requestPath) ||
    preg_match('#\\.(?:php|env|ini|log|sql|bak|backup)$#i', $requestPath)
) {
    http_response_code(404);
    exit;
}

$relativePath = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $requestPath), DIRECTORY_SEPARATOR);
$requestedFile = realpath($publicRoot . DIRECTORY_SEPARATOR . $relativePath);
$publicPrefix = rtrim($publicRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

if (
    $requestedFile !== false &&
    str_starts_with($requestedFile, $publicPrefix) &&
    is_file($requestedFile)
) {
    return false;
}

// El router MVC toma la ruta desde PATH_INFO.
$_SERVER['PATH_INFO'] = $requestPath;
require $publicRoot . DIRECTORY_SEPARATOR . 'index.php';
