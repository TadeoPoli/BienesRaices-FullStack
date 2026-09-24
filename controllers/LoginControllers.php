<?php
namespace Controllers;
use MVC\Router;
use Model\Admin;

class LoginControllers {
    public static function login(Router $router){

        $errores = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (!validarTokenCsrf()) {
                $errores[] = 'La solicitud no es válida. Intentá nuevamente.';
            } else {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $auth = new Admin([
                    'email' => is_scalar($email) ? (string) $email : '',
                    'password' => is_scalar($password) ? (string) $password : ''
                ]);

                $errores = $auth->validar();

                if(empty($errores)) {
                    // Verificar si el usuario existe
                    $usuario = $auth->existeUsuario();

                    if($usuario === false) {
                        $errores = Admin::getErrores();
                    } else {
                        $autenticado = $auth->comprobarPassword($usuario);

                        if($autenticado) {
                            $auth->autenticar();
                        } else {
                            $errores = Admin::getErrores();
                        }
                    }
                }
            }
        }
        $router->render('auth/login', [
            'errores' => $errores
        ]);
    }

    public static function logout(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['usuario'], $_SESSION['login']);
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();

        header('Location: /');
        exit;
    }
}
