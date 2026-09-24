<?php

namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use PHPMailer\PHPMailer\PHPMailer;

class PaginaControllers {
    public static function index(Router $router) {
        $propiedades = Propiedad::get(3);
        $inicio = true;

        $router->render('paginas/index', [
            'propiedades' => $propiedades,
            'inicio' => $inicio
        ]);
    }
    public static function nosotros(Router $router) {
        $router->render('paginas/nosotros');
    }
    public static function propiedades(Router $router) {
        $propiedades = Propiedad::all();
        $router->render('paginas/propiedades', [
            'propiedades' => $propiedades
        ]);
    }
    public static function propiedad(Router $router) {

        $id = validar('/propiedades');

        // Buscar la propiedad por su id
        $propiedad = Propiedad::find($id);
        if (!$propiedad) {
            header('Location: /propiedades');
            exit;
        }

        $router->render('paginas/propiedad', [
            'propiedad' => $propiedad
        ]);
    }
    public static function blog(Router $router) {
        $router->render('paginas/blog');
    }
    public static function entrada(Router $router) {
         $router->render('paginas/entrada');
    }
    public static function contacto(Router $router) {

        $mensaje = null;
        $tipoMensaje = null;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validarTokenCsrf()) {
                $mensaje = 'La solicitud no es válida. Intentá nuevamente.';
                $tipoMensaje = 'error';
                $router->render('paginas/contacto', [
                    'mensaje' => $mensaje,
                    'tipoMensaje' => $tipoMensaje
                ]);
                return;
            }

            $respuesta = $_POST['contacto'] ?? [];
            if (!is_array($respuesta)) {
                $respuesta = [];
            }

            $errores = self::validarContacto($respuesta);
            if (!empty($errores)) {
                $router->render('paginas/contacto', [
                    'mensaje' => implode(' ', $errores),
                    'tipoMensaje' => 'error'
                ]);
                return;
            }

            $escaparCorreo = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);

            // Crear una instancia de PHPMaile
            $mail = new PHPMailer();

            // Configurar SMTP
            $mail->isSMTP();
            $mail->Host = getenv('SMTP_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USERNAME');
            $mail->Password = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = getenv('SMTP_ENCRYPTION');
            $mail->Port = (int) getenv('SMTP_PORT');

            // Configurar contenido Mail
            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress(getenv('MAIL_TO'), getenv('MAIL_TO_NAME'));
            $mail->Subject = 'Tienes un Nuevo Mensaje';

            // habilitar HTML

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            // Definir Contenido
            $contenido = '<html>';
            $contenido .= '<p> Tienes un nuevo Mensaje </p>';
            $contenido .= '<p>Nombre: ' . $escaparCorreo($respuesta['nombre'] ?? '') . '</p>';

            // Enviar de forma condiconal algunos campos de email o telofono
            if(($respuesta['contacto'] ?? '') === 'telefono'){
                $contenido .= '<p>Eligio ser Contactado por Telefono</p>';
                $contenido .= '<p>Telefono: ' . $escaparCorreo($respuesta['telefono'] ?? '') . '</p>';
                $contenido .= '<p>Fecha: ' . $escaparCorreo($respuesta['fecha'] ?? '') . '</p>';
                $contenido .= '<p>Hora: ' . $escaparCorreo($respuesta['hora'] ?? '') . '</p>';
            } else {
                // Es email, entonces agragamos el campo email
                 $contenido .= '<p>Eligio ser Contactado por Email</p>';
                 $contenido .= '<p>Email: ' . $escaparCorreo($respuesta['email'] ?? '') . '</p>';
            }

            $contenido .= '<p>Mensaje: ' . $escaparCorreo($respuesta['mensaje'] ?? '') . '</p>';
            $contenido .= '<p>Vende o Compra: ' . $escaparCorreo($respuesta['tipo'] ?? '') . '</p>';
            $contenido .= '<p>Precio o Presupuesto: $' . $escaparCorreo($respuesta['precio'] ?? '') . '</p>';
            $contenido .= '<p>Prefieres ser Contactado por: ' . $escaparCorreo($respuesta['contacto'] ?? '') . '</p>';
            $contenido .= '</html>';

            $mail->Body = $contenido;
            $mail->AltBody = 'Esto es texto sin HTML';

            // Enviar Email
            if($mail->send()) {
                $mensaje = "Mensaje Enviado Correctamente";
                $tipoMensaje = 'exito';
            } else {
                $mensaje = "El Mensaje No se pudo Enviar";
                $tipoMensaje = 'error';
            }
        }
        $router->render('paginas/contacto', [
            'mensaje' => $mensaje,
            'tipoMensaje' => $tipoMensaje
        ]);
    }

    private static function validarContacto(array $respuesta): array
    {
        $errores = [];
        $nombre = $respuesta['nombre'] ?? null;
        $mensaje = $respuesta['mensaje'] ?? null;
        $precio = $respuesta['precio'] ?? null;
        $tipo = $respuesta['tipo'] ?? null;
        $contacto = $respuesta['contacto'] ?? null;

        if (!is_string($nombre) || trim($nombre) === '' || mb_strlen($nombre) > 100) {
            $errores[] = 'Ingresá un nombre válido de hasta 100 caracteres.';
        }
        if (!is_string($mensaje) || mb_strlen(trim($mensaje)) < 10 || mb_strlen($mensaje) > 2000) {
            $errores[] = 'El mensaje debe tener entre 10 y 2000 caracteres.';
        }
        if (!is_scalar($precio) || !is_numeric($precio) || (float) $precio <= 0 || (float) $precio > 9999999999.99) {
            $errores[] = 'Ingresá un presupuesto válido mayor que cero.';
        }
        if (!is_string($tipo) || !in_array($tipo, ['compra', 'vende'], true)) {
            $errores[] = 'Indicá si querés comprar o vender.';
        }
        if ($contacto === 'email') {
            $email = $respuesta['email'] ?? null;
            if (!is_string($email) || mb_strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'Ingresá un email de contacto válido.';
            }
        } elseif ($contacto === 'telefono') {
            $telefono = $respuesta['telefono'] ?? null;
            $fecha = $respuesta['fecha'] ?? null;
            $hora = $respuesta['hora'] ?? null;
            if (!is_string($telefono) || !preg_match('/^\+?[0-9 ()-]{8,20}$/', $telefono) || preg_match_all('/\d/', $telefono) < 8) {
                $errores[] = 'Ingresá un teléfono de contacto válido.';
            }
            $fechaValida = is_string($fecha) && \DateTime::createFromFormat('!Y-m-d', $fecha)?->format('Y-m-d') === $fecha;
            $horaValida = is_string($hora) && \DateTime::createFromFormat('!H:i', $hora)?->format('H:i') === $hora;
            if (!$fechaValida || !$horaValida) {
                $errores[] = 'Ingresá una fecha y hora de contacto válidas.';
            }
        } else {
            $errores[] = 'Elegí un medio de contacto válido.';
        }

        return $errores;
    }
}
