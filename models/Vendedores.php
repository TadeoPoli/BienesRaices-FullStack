<?php

namespace Model;

class Vendedores extends ActiveRecord
{

    protected static $tabla = 'vendedores';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'telefono'];

    public $id;
    public $nombre;
    public $apellido;
    public $telefono;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
    }

    public function validar()
    {
        self::$errores = [];

        if (!is_string($this->nombre) || trim($this->nombre) === '') {
            self::$errores[] = 'Debes añadir un nombre.';
        } elseif (mb_strlen($this->nombre) > 100) {
            self::$errores[] = 'El nombre no puede superar los 100 caracteres.';
        }

        if (!is_string($this->apellido) || trim($this->apellido) === '') {
            self::$errores[] = 'Debes añadir un apellido.';
        } elseif (mb_strlen($this->apellido) > 100) {
            self::$errores[] = 'El apellido no puede superar los 100 caracteres.';
        }

        if (!is_string($this->telefono) || !preg_match('/^\+?[0-9 ()-]{8,20}$/', $this->telefono) || preg_match_all('/\d/', $this->telefono) < 8) {
            self::$errores[] = 'Formato de teléfono no válido.';
        }


        return self::$errores;
    }

    public static function tienePropiedades(int $id): bool
    {
        $stmt = self::$db->prepare('SELECT `id` FROM `propiedades` WHERE `vendedores_id` = ? LIMIT 1');

        if (!$stmt) {
            error_log('Error de base de datos durante la preparación de verificación de vendedor: ' . self::$db->error);
            return true;
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            error_log('Error de base de datos durante la verificación de vendedor: ' . $stmt->error);
            $stmt->close();
            return true;
        }

        $resultado = $stmt->get_result();
        $tienePropiedades = $resultado->num_rows > 0;
        $resultado->free();
        $stmt->close();

        return $tienePropiedades;
    }
}
