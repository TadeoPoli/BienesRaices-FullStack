<?php

namespace Controllers;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as Image;
use Model\Propiedad;
use Model\Vendedores;
use MVC\Router;

class PropiedadControllers
{
    private const TAMANIO_MAXIMO_IMAGEN = 5242880;
    private const ANCHO_MAXIMO_IMAGEN = 5000;
    private const ALTO_MAXIMO_IMAGEN = 5000;
    private const PIXELES_MAXIMOS_IMAGEN = 12000000;

    public static function index(Router $router)
    {
        $router->render('propiedades/admin', [
            'propiedades' => Propiedad::all(),
            'flash' => obtenerFlash(),
            'vendedores' => Vendedores::all()
        ]);
    }

    public static function crear(Router $router)
    {
        $propiedad = new Propiedad;
        $vendedores = Vendedores::all();
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarTokenCsrf()) {
                guardarFlash('error', 'La solicitud no es válida. Intentá nuevamente.');
                header('Location: /admin');
                exit;
            }

            $propiedad = new Propiedad(self::datosPropiedad());
            [$nombreImagen, $imagen, $erroresImagen, $imagenFueEnviada] = self::prepararImagen(self::archivoImagen());
            $errores = array_merge($erroresImagen, $propiedad->validar($imagen !== null, $imagenFueEnviada));

            if (empty($errores)) {
                $propiedad->setImagen($nombreImagen);

                if (!self::guardarImagen($imagen, $nombreImagen)) {
                    $errores[] = 'No se pudo procesar la imagen. Intentá nuevamente.';
                } elseif ($propiedad->guardar()) {
                    guardarFlash('exito', 'Propiedad creada correctamente.');
                    header('Location: /admin');
                    exit;
                } else {
                    $propiedad->borrarImagen($nombreImagen);
                    $errores[] = 'No se pudo crear la propiedad. Intentá nuevamente.';
                }
            }
        }

        $router->render('propiedades/crear', compact('propiedad', 'vendedores', 'errores'));
    }

    public static function actualizar(Router $router)
    {
        $id = validar('/admin');
        $propiedad = Propiedad::find($id);
        if (!$propiedad) {
            guardarFlash('error', 'La propiedad solicitada no existe.');
            header('Location: /admin');
            exit;
        }

        $vendedores = Vendedores::all();
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarTokenCsrf()) {
                guardarFlash('error', 'La solicitud no es válida. Intentá nuevamente.');
                header('Location: /admin');
                exit;
            }

            $propiedad->sincronizar(self::datosPropiedad());
            [$nombreImagen, $imagen, $erroresImagen, $imagenFueEnviada] = self::prepararImagen(self::archivoImagen());
            $errores = array_merge($erroresImagen, $propiedad->validar($imagen !== null, $imagenFueEnviada));

            if (empty($errores)) {
                $imagenAnterior = $propiedad->imagen;

                if ($imagen !== null) {
                    if (!self::guardarImagen($imagen, $nombreImagen)) {
                        $errores[] = 'No se pudo procesar la imagen. Intentá nuevamente.';
                    } else {
                        $propiedad->setImagen($nombreImagen);
                    }
                }

                if (empty($errores) && $propiedad->guardar()) {
                    if ($imagen !== null && $imagenAnterior !== $nombreImagen) {
                        $propiedad->borrarImagen($imagenAnterior);
                    }

                    guardarFlash('exito', 'Propiedad actualizada correctamente.');
                    header('Location: /admin');
                    exit;
                }

                if ($imagen !== null) {
                    $propiedad->borrarImagen($nombreImagen);
                    $propiedad->setImagen($imagenAnterior);
                }

                if (empty($errores)) {
                    $errores[] = 'No se pudo actualizar la propiedad. Intentá nuevamente.';
                }
            }
        }

        $router->render('propiedades/actualizar', compact('propiedad', 'errores', 'vendedores'));
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (!validarTokenCsrf()) {
            guardarFlash('error', 'La solicitud no es válida. Intentá nuevamente.');
        } elseif (($_POST['tipo'] ?? '') !== 'propiedad' || ($id = validarId($_POST['id'] ?? null)) === null) {
            guardarFlash('error', 'La propiedad indicada no es válida.');
        } else {
            $propiedad = Propiedad::find($id);
            if ($propiedad && $propiedad->eliminar()) {
                guardarFlash('exito', 'Propiedad eliminada correctamente.');
            } else {
                guardarFlash('error', 'No se pudo eliminar la propiedad.');
            }
        }

        header('Location: /admin');
        exit;
    }

    private static function datosPropiedad(): array
    {
        $datos = $_POST['propiedad'] ?? [];
        if (!is_array($datos)) {
            return [];
        }

        $permitidos = ['titulo', 'precio', 'descripcion', 'habitaciones', 'wc', 'estacionamientos', 'vendedores_id'];
        $resultado = [];
        foreach ($permitidos as $campo) {
            $valor = $datos[$campo] ?? '';
            $resultado[$campo] = is_scalar($valor) ? (string) $valor : '';
        }

        return $resultado;
    }

    private static function archivoImagen(): ?array
    {
        $archivo = $_FILES['propiedad'] ?? null;
        if (!is_array($archivo)) {
            return null;
        }

        $resultado = [];
        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $campo) {
            $valor = $archivo[$campo]['imagen'] ?? null;
            $resultado[$campo] = is_scalar($valor) ? $valor : null;
        }

        return $resultado;
    }

    private static function prepararImagen(?array $archivo): array
    {
        $sinArchivo = $archivo === null || (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE;
        if ($sinArchivo) {
            return [null, null, [], false];
        }

        if ((int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [null, null, ['La imagen no se pudo subir correctamente.'], true];
        }

        $temporal = $archivo['tmp_name'] ?? null;
        $tamanio = $archivo['size'] ?? null;
        if (!is_string($temporal) || !is_uploaded_file($temporal) || (!is_int($tamanio) && !ctype_digit((string) $tamanio))) {
            return [null, null, ['El archivo de imagen recibido no es válido.'], true];
        }

        $tamanio = (int) $tamanio;
        if ($tamanio <= 0 || $tamanio > self::TAMANIO_MAXIMO_IMAGEN) {
            return [null, null, ['La imagen debe pesar entre 1 byte y 5 MB.'], true];
        }

        $datosImagen = @getimagesize($temporal);
        if ($datosImagen === false) {
            return [null, null, ['El archivo no es una imagen válida.'], true];
        }

        [$ancho, $alto, $tipo] = $datosImagen;
        $tiposPermitidos = [IMAGETYPE_JPEG => 'image/jpeg', IMAGETYPE_PNG => 'image/png', IMAGETYPE_WEBP => 'image/webp'];
        if (!isset($tiposPermitidos[$tipo]) || $ancho <= 0 || $alto <= 0 || $ancho > self::ANCHO_MAXIMO_IMAGEN || $alto > self::ALTO_MAXIMO_IMAGEN || $ancho * $alto > self::PIXELES_MAXIMOS_IMAGEN) {
            return [null, null, ['La imagen supera los límites permitidos de dimensiones.'], true];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if ($finfo->file($temporal) !== $tiposPermitidos[$tipo]) {
            return [null, null, ['El tipo real del archivo de imagen no es válido.'], true];
        }

        try {
            $imagen = (new Image(Driver::class))->read($temporal)->cover(800, 600);
        } catch (\Throwable $error) {
            error_log('No se pudo procesar una imagen subida: ' . $error->getMessage());
            return [null, null, ['No se pudo procesar la imagen enviada.'], true];
        }

        return [bin2hex(random_bytes(16)) . '.jpg', $imagen, [], true];
    }

    private static function guardarImagen($imagen, ?string $nombreImagen): bool
    {
        if ($imagen === null || $nombreImagen === null) {
            return false;
        }

        if (!is_dir(CARPETA_IMAGENES) && !mkdir(CARPETA_IMAGENES, 0755, true) && !is_dir(CARPETA_IMAGENES)) {
            error_log('No se pudo crear la carpeta de imágenes.');
            return false;
        }

        $ruta = rtrim(CARPETA_IMAGENES, '/\\') . DIRECTORY_SEPARATOR . basename($nombreImagen);
        try {
            $imagen->toJpeg(85)->save($ruta);
            clearstatcache(true, $ruta);
            return is_file($ruta) && filesize($ruta) > 0;
        } catch (\Throwable $error) {
            error_log('No se pudo guardar una imagen procesada: ' . $error->getMessage());
            return false;
        }
    }
}
