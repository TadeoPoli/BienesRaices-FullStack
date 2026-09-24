<?php

namespace Model;

class Propiedad extends ActiveRecord
{

    protected static $tabla = 'propiedades';
    protected static $columnasDB = ['id', 'titulo', 'precio', 'imagen', 'descripcion', 'habitaciones', 'wc', 'estacionamientos', 'creado', 'vendedores_id'];

    public $id;
    public $titulo;
    public $precio;
    public $imagen;
    public $descripcion;
    public $habitaciones;
    public $wc;
    public $estacionamientos;
    public $creado;
    public $vendedores_id;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->titulo = $args['titulo'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->habitaciones = $args['habitaciones'] ?? '';
        $this->wc = $args['wc'] ?? '';
        $this->estacionamientos = $args['estacionamientos'] ?? '';
        $this->creado = date('Y/m/d');
        $this->vendedores_id = $args['vendedores_id'] ?? '';
    }

    public function validar(bool $imagenNuevaValida = false, bool $imagenFueEnviada = false)
    {
        self::$errores = [];

        if (!is_string($this->titulo) || trim($this->titulo) === '') {
            self::$errores[] = 'Debes añadir un título.';
        } elseif (mb_strlen($this->titulo) > 255) {
            self::$errores[] = 'El título no puede superar los 255 caracteres.';
        }

        if (!is_scalar($this->precio) || !is_numeric($this->precio) || (float) $this->precio <= 0 || (float) $this->precio > 9999999999.99) {
            self::$errores[] = 'El precio debe ser un número mayor que cero.';
        }

        if (!is_string($this->descripcion) || mb_strlen(trim($this->descripcion)) < 50) {
            self::$errores[] = 'La descripción es obligatoria y debe tener al menos 50 caracteres.';
        } elseif (mb_strlen($this->descripcion) > 5000) {
            self::$errores[] = 'La descripción no puede superar los 5000 caracteres.';
        }

        $this->validarCantidad($this->habitaciones, 'habitaciones');
        $this->validarCantidad($this->wc, 'baños');
        $this->validarCantidad($this->estacionamientos, 'estacionamientos');

        $vendedorId = validarId($this->vendedores_id);
        if ($vendedorId === null) {
            self::$errores[] = 'Debes elegir un vendedor válido.';
        } elseif (!Vendedores::find($vendedorId)) {
            self::$errores[] = 'El vendedor seleccionado no existe.';
        }

        if (!$this->imagen && !$imagenNuevaValida && !$imagenFueEnviada) {
            self::$errores[] = 'La imagen es obligatoria.';
        }

        return self::$errores;
    }

    private function validarCantidad($valor, string $campo): void
    {
        $cantidad = is_scalar($valor) ? filter_var($valor, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 255]
        ]) : false;

        if ($cantidad === false) {
            self::$errores[] = 'El número de ' . $campo . ' debe ser un entero entre 0 y 255.';
        }
    }

}
