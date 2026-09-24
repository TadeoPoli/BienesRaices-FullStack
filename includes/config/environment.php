<?php

function cargarEntorno(string $archivo): void
{
    if (!is_file($archivo)) {
        return;
    }

    $variables = parse_ini_file($archivo, false, INI_SCANNER_RAW);

    if ($variables === false) {
        throw new RuntimeException('No se pudo leer el archivo de configuración local.');
    }

    foreach ($variables as $nombre => $valor) {
        if (!is_string($valor)) {
            continue;
        }

        putenv("{$nombre}={$valor}");
        $_ENV[$nombre] = $valor;
    }
}

cargarEntorno(__DIR__ . '/../../.env');
