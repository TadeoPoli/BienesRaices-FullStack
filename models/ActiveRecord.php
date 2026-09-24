<?php

namespace Model;

class ActiveRecord
{

    // Base de Datos
    protected static $db;
    protected static $columnasDB = [];
    protected static $tabla = '';

    // errores o validacion
    protected static $errores = [];



    // Definir la conexion a la BD
    public static function setDB($database)
    {
        self::$db = $database;
    }



    public function guardar(): bool
    {
        if (!is_null($this->id)) {
            //actualizar
            return $this->actualizar();
        } else {
            // Creando nuevo registro
            return $this->crear();
        }
    }

    public function crear(): bool
    {
        $atributos = $this->atributos();
        $columnas = array_keys($atributos);

        $query = 'INSERT INTO ' . static::tablaSegura() . ' (';
        $query .= join(', ', array_map([static::class, 'columnaSegura'], $columnas));
        $query .= ') VALUES (' . join(', ', array_fill(0, count($columnas), '?')) . ')';

        return static::ejecutarCambio($query, array_values($atributos));
    }

    public function actualizar(): bool
    {
        $atributos = $this->atributos();
        $valores = [];
        foreach (array_keys($atributos) as $columna) {
            $valores[] = static::columnaSegura($columna) . ' = ?';
        }

        $query = 'UPDATE ' . static::tablaSegura() . ' SET ' . join(', ', $valores) . ' WHERE `id` = ? LIMIT 1';
        $parametros = array_values($atributos);
        $parametros[] = (int) $this->id;

        return static::ejecutarCambio($query, $parametros);
    }

    // Eliminar un Registro

    public function eliminar(): bool
    {
        $query = 'DELETE FROM ' . static::tablaSegura() . ' WHERE `id` = ? LIMIT 1';
        $resultado = static::ejecutarCambio($query, [(int) $this->id], true);

        if ($resultado === true) {
            $this->borrarImagen();
        }

        return $resultado;
    }

    // Indentifica y Une los Atributos de la BD

    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarDatos()
    {
        return $this->atributos();
    }

    // Validacion
    public static function getErrores()
    {
        return static::$errores;
    }

    public function validar()
    {
        static::$errores = [];
        return static::$errores;
    }

    public function setImagen($imagen)
    {
        if (is_string($imagen) && $imagen !== '') {
            $this->imagen = basename($imagen);
        }
    }

    public function borrarImagen(?string $nombreImagen = null): void
    {
        if (!property_exists($this, 'imagen')) {
            return;
        }

        $nombreImagen = basename($nombreImagen ?? (string) ($this->imagen ?? ''));
        if ($nombreImagen === '') {
            return;
        }

        $carpeta = realpath(CARPETA_IMAGENES);
        if ($carpeta === false || $this->imagenEnUsoPorOtroRegistro($nombreImagen)) {
            return;
        }

        $ruta = $carpeta . DIRECTORY_SEPARATOR . $nombreImagen;
        if (is_file($ruta) && realpath(dirname($ruta)) === $carpeta) {
            unlink($ruta);
        }
    }

    protected function imagenEnUsoPorOtroRegistro(string $nombreImagen): bool
    {
        if (static::$tabla !== 'propiedades') {
            return false;
        }

        $id = (int) ($this->id ?? 0);
        $stmt = self::$db->prepare('SELECT `id` FROM `propiedades` WHERE `imagen` = ? AND `id` <> ? LIMIT 1');
        if (!$stmt) {
            static::registrarErrorBD('verificación de imagen compartida');
            return true;
        }

        $stmt->bind_param('si', $nombreImagen, $id);
        if (!$stmt->execute()) {
            static::registrarErrorBD('verificación de imagen compartida');
            $stmt->close();
            return true;
        }

        $resultado = $stmt->get_result();
        $enUso = $resultado->num_rows > 0;
        $resultado->free();
        $stmt->close();

        return $enUso;
    }

    // Lista todos los registros

    public static function all()
    {
        $query = 'SELECT * FROM ' . static::tablaSegura();

        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    // Obtiene determinado numero de registros
     public static function get($cantidad)
    {
        $query = 'SELECT * FROM ' . static::tablaSegura() . ' LIMIT ?';
        $resultado = static::consultarSQLPreparada($query, [(int) $cantidad]);

        return $resultado;
    }

    // Busca un registro po su id
    public static function find($id)
    {
        $query = 'SELECT * FROM ' . static::tablaSegura() . ' WHERE `id` = ? LIMIT 1';
        $resultado = static::consultarSQLPreparada($query, [(int) $id]);
        return array_shift($resultado);
    }

    public static function consultarSQL($query)
    {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        if (!$resultado) {
            static::registrarErrorBD('consulta interna');
            return [];
        }

        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // Liberar la memoria
        $resultado->free();

        // Retornar los resultados
        return $array;
    }

    protected static function consultarSQLPreparada(string $query, array $parametros = []): array
    {
        $stmt = self::$db->prepare($query);

        if (!$stmt) {
            static::registrarErrorBD('preparación de consulta');
            return [];
        }

        if (!static::vincularParametros($stmt, $parametros) || !$stmt->execute()) {
            static::registrarErrorBD('ejecución de consulta');
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        $resultado->free();
        $stmt->close();

        return $array;
    }

    protected static function ejecutarCambio(string $query, array $parametros, bool $requiereFilasAfectadas = false): bool
    {
        $stmt = self::$db->prepare($query);

        if (!$stmt) {
            static::registrarErrorBD('preparación de cambio');
            return false;
        }

        if (!static::vincularParametros($stmt, $parametros) || !$stmt->execute()) {
            static::registrarErrorBD('ejecución de cambio');
            $stmt->close();
            return false;
        }

        $resultado = !$requiereFilasAfectadas || $stmt->affected_rows === 1;
        $stmt->close();

        return $resultado;
    }

    protected static function vincularParametros($stmt, array $parametros): bool
    {
        if (empty($parametros)) {
            return true;
        }

        $tipos = '';
        foreach ($parametros as $parametro) {
            $tipos .= is_int($parametro) ? 'i' : (is_float($parametro) ? 'd' : 's');
        }

        $referencias = [];
        foreach ($parametros as $indice => $parametro) {
            $referencias[$indice] = &$parametros[$indice];
        }

        return $stmt->bind_param($tipos, ...$referencias);
    }

    protected static function tablaSegura(): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', static::$tabla)) {
            throw new \RuntimeException('Configuración de tabla inválida.');
        }

        return '`' . static::$tabla . '`';
    }

    protected static function columnaSegura(string $columna): string
    {
        if (!in_array($columna, static::$columnasDB, true) || $columna === 'id') {
            throw new \RuntimeException('Configuración de columna inválida.');
        }

        return '`' . $columna . '`';
    }

    protected static function registrarErrorBD(string $contexto): void
    {
        error_log('Error de base de datos durante ' . $contexto . ': ' . self::$db->error);
    }

    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Sincroniza el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
