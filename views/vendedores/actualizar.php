<main class="contenedor seccion">
    <h1>Actualizar Vendedor</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error): ?>
        <div class="alerta error">
            <?php echo s($error); ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST">
        <?php include 'formulario.php';?>

        <input type="submit" value="Actualizar Vendedor" class="boton boton-verde">
    </form>
</main>
