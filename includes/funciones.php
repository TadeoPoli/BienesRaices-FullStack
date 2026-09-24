<?php

define('FUNCIONES_URL', __DIR__ . '/funciones.php');
define('CARPETA_IMAGENES', $_SERVER['DOCUMENT_ROOT'] . '/imagenes/');

function estaAutenticado()
{
    session_start();
    if (!$_SESSION['login']) {
        header('Location: /');
    } else {
        return true;
    }
}

function debuguear($variable)
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa del HTML

function s($html): string
{
    return htmlspecialchars((string) $html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
}

function urlImagen($nombreArchivo): string
{
    return '/imagenes/' . rawurlencode(basename((string) $nombreArchivo));
}

// Validar tipo Contenido
function validarTipoContenido($tipo)
{
    $tipos = ['vendedor', 'propiedad'];

    return in_array($tipo, $tipos);
}

// Muestra los mensajes

function mostrarNotificacion($codigo)
{
    $mensaje = '';

    switch ($codigo) {
        case 1:
            $mensaje = 'Creado Correctamente';
            break;
        case 2:
            $mensaje = 'Actualizado Correctamente';
            break;
        case 3:
            $mensaje = 'Eliminado Correctamente';
            break;
        case 4:
            $mensaje = 'No es posible eliminar este vendedor porque tiene propiedades asociadas.';
            break;
        default:
            $mensaje = false;
            break;
    }

    return $mensaje;
}

function guardarFlash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

function obtenerTokenCsrf(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validarTokenCsrf(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $tokenSesion = $_SESSION['csrf_token'] ?? '';
    $tokenSolicitud = $_POST['csrf_token'] ?? '';

    return is_string($tokenSesion)
        && is_string($tokenSolicitud)
        && $tokenSesion !== ''
        && hash_equals($tokenSesion, $tokenSolicitud);
}

function obtenerFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function validar(string $url)
{
    $id = validarId($_GET['id'] ?? null);

    if ($id === null) {
        header("Location: $url");
        exit;
    }

    return $id;
}

function validarId($valor): ?int
{
    if (!is_scalar($valor)) {
        return null;
    }

    $id = filter_var($valor, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    return $id === false ? null : $id;
}
