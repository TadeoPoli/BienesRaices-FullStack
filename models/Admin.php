<?php

namespace Model;

class Admin extends ActiveRecord {
    public const MENSAJE_CREDENCIALES_INVALIDAS = 'Correo electrónico o contraseña incorrectos';
    private const HASH_REFERENCIA_LOGIN = '$2y$12$W7tpwu5Ruj8uxN8wHQY5dOmRRWtRkaOGr/NJLKsxru0H/mUG7je4.';

    // Base de Datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'email', 'password'];

    public $id;
    public $email;
    public $password;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
    }

    public function validar()
    {
        self::$errores = [];

        if (!is_string($this->email) || $this->email === '') {
            self::$errores[] = 'El email es obligatorio.';
        } elseif (mb_strlen($this->email) > 255 || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$errores[] = 'Ingresá un email válido.';
        }

        if (!is_string($this->password) || $this->password === '') {
            self::$errores[] = 'El password es obligatorio.';
        } elseif (strlen($this->password) > 255) {
            self::$errores[] = 'El password no puede superar los 255 caracteres.';
        }

        return self::$errores;
    }

    public function existeUsuario() {
        $query = 'SELECT * FROM `usuarios` WHERE `email` = ? LIMIT 1';
        $stmt = self::$db->prepare($query);

        if (!$stmt) {
            error_log('Error de base de datos durante la preparación de login: ' . self::$db->error);
            self::$errores[] = 'No fue posible iniciar sesión. Intentá nuevamente.';
            return false;
        }

        $stmt->bind_param('s', $this->email);

        if (!$stmt->execute()) {
            error_log('Error de base de datos durante el login: ' . $stmt->error);
            self::$errores[] = 'No fue posible iniciar sesión. Intentá nuevamente.';
            $stmt->close();
            return false;
        }

        $resultado = $stmt->get_result();

        $usuario = $resultado->fetch_object();
        $stmt->close();

        return $usuario ?: null;
    }

    public function comprobarPassword($usuario) {
        $hash = is_object($usuario) && isset($usuario->password) && is_string($usuario->password)
            ? $usuario->password
            : self::HASH_REFERENCIA_LOGIN;

        $autenticado = password_verify($this->password, $hash);

        if(!$autenticado || !$usuario) {
            self::$errores[] = self::MENSAJE_CREDENCIALES_INVALIDAS;
            return false;
        }

        return true;
    }

    public function autenticar() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);

        // LLenar el arreglo de session
        $_SESSION['usuario'] = $this->email;
        $_SESSION['login'] = true;

        header('Location: /admin');
        exit;
    }
}
