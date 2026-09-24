<?php

namespace Controllers;

use MVC\Router;
use Model\Vendedores;

class VendedorControllers
{
    public static function crear(Router $router)
    {

        $errores = Vendedores::getErrores();

        $vendedor = new Vendedores();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarTokenCsrf()) {
                guardarFlash('error', 'La solicitud no es válida. Intentá nuevamente.');
                header('Location: /admin');
                exit;
            }

            // Crear una nueva instacia
            $vendedor = new Vendedores(self::datosVendedor());

            // Validar que no haya cambios
            $errores = $vendedor->validar();

            // No hay errores
            if (empty($errores)) {
                if ($vendedor->guardar()) {
                    guardarFlash('exito', 'Vendedor creado correctamente.');
                    header('Location: /admin');
                    exit;
                }

                $errores[] = 'No se pudo crear el vendedor. Intentá nuevamente.';
            }
        }

        $router->render('vendedores/crear', [
            'errores' => $errores,
            'vendedor' => $vendedor
        ]);
    }

    public static function actualizar(Router $router)
    {
        $errores = Vendedores::getErrores();
        $id = validar('/admin');

        //Obtener datos del vendedor
        $vendedor = Vendedores::find($id);
        if (!$vendedor) {
            guardarFlash('error', 'El vendedor solicitado no existe.');
            header('Location: /admin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarTokenCsrf()) {
                guardarFlash('error', 'La solicitud no es válida. Intentá nuevamente.');
                header('Location: /admin');
                exit;
            }

            // Asignar los valores
            $args = self::datosVendedor();
            // Sincronizar objeto en memoria con lo que el usuario escribio
            $vendedor->sincronizar($args);
            // Validacion
            $errores = $vendedor->validar();

            if (empty($errores)) {
                if ($vendedor->guardar()) {
                    guardarFlash('exito', 'Vendedor actualizado correctamente.');
                    header('Location: /admin');
                    exit;
                }

                $errores[] = 'No se pudo actualizar el vendedor. Intentá nuevamente.';
            }
        }

        $router->render('vendedores/actualizar', [
            'errores' => $errores,
            'vendedor' => $vendedor
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarTokenCsrf()) {
                guardarFlash('error', 'La solicitud no es válida. Intentá nuevamente.');
                header('Location: /admin');
                exit;
            }

            $id = validarId($_POST['id'] ?? null);

            if ($id !== null && ($_POST['tipo'] ?? '') === 'vendedor') {
                if (Vendedores::tienePropiedades($id)) {
                    // NO SE PUEDE ELIMINAR
                    guardarFlash('error', 'No es posible eliminar este vendedor porque tiene propiedades asociadas.');
                    header('Location: /admin');
                    exit;
                }

                // 2. Si no tiene propiedades → se elimina normalmente
                $vendedor = Vendedores::find($id);

                if ($vendedor && $vendedor->eliminar()) {
                    guardarFlash('exito', 'Vendedor eliminado correctamente.');
                } else {
                    guardarFlash('error', 'No se pudo eliminar el vendedor.');
                }

                header('Location: /admin');
                exit;
            }

            guardarFlash('error', 'El vendedor indicado no es válido.');
            header('Location: /admin');
            exit;
        }
    }

    private static function datosVendedor(): array
    {
        $datos = $_POST['vendedor'] ?? [];
        if (!is_array($datos)) {
            return [];
        }

        $resultado = [];
        foreach (['nombre', 'apellido', 'telefono'] as $campo) {
            $valor = $datos[$campo] ?? '';
            $resultado[$campo] = is_scalar($valor) ? (string) $valor : '';
        }

        return $resultado;
    }
}
