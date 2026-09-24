<?php

declare(strict_types=1);

namespace MVC;

class Router
{
    public array $rutasGET = [];
    public array $rutasPOST = [];

    public function get($url, $fn): void
    {
        $this->rutasGET[$url] = $fn;
    }

    public function post($url, $fn): void
    {
        $this->rutasPOST[$url] = $fn;
    }

    public function comprobarRutas(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $auth = $_SESSION['login'] ?? false;
        $rutasProtegidas = [
            '/admin',
            '/propiedades/crear',
            '/propiedades/actualizar',
            '/propiedades/eliminar',
            '/vendedores/crear',
            '/vendedores/actualizar',
            '/vendedores/eliminar',
        ];

        $urlActual = $_SERVER['PATH_INFO'] ?? '/';
        $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $fn = $metodo === 'GET'
            ? ($this->rutasGET[$urlActual] ?? null)
            : ($this->rutasPOST[$urlActual] ?? null);

        if (in_array($urlActual, $rutasProtegidas, true) && !$auth) {
            header('Location: /login');
            exit;
        }

        if ($fn) {
            call_user_func($fn, $this);
            return;
        }

        http_response_code(404);
        echo 'Página no encontrada';
    }

    public function render($view, $datos = []): void
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include dirname(__DIR__) . "/views/$view.php";
        $contenido = ob_get_clean();
        include dirname(__DIR__) . '/views/layout.php';
    }
}
