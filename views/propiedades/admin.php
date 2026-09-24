<main class="contenedor seccion">
  <h1>Administrador de Bienes Raices</h1>

  <?php if ($flash): ?>
    <p class="alerta <?php echo $flash['tipo'] === 'exito' ? 'exito' : 'error'; ?>"><?php echo s($flash['mensaje']); ?></p>
  <?php endif; ?>

  <a href="/propiedades/crear" class="boton boton-verde">Nueva Propiedad</a>
  <a href="/vendedores/crear" class="boton boton-azul">Nuevo Vendedor</a>

  <h2>Propiedades</h2>
  <table class="propiedades">
    <thead>
      <tr>
        <th>ID</th>
        <th>Titulo</th>
        <th>Imagenes</th>
        <th>Precio</th>
        <th>Acciones</th>
      </tr>
    </thead>

    <tbody> <!-- Mostar Los Resultados -->
      <?php foreach ($propiedades as $propiedad): ?>
        <tr>
          <td><?php echo s($propiedad->id); ?></td>
          <td><?php echo s($propiedad->titulo); ?></td>
          <td><img src="<?php echo urlImagen($propiedad->imagen); ?>" class="imagen-tabla" alt=""></td>
          <td>$ <?php echo s($propiedad->precio); ?></td>
          <td>
            <form method="POST" class="w-100" action="/propiedades/eliminar" data-confirmar-eliminacion data-confirmacion="¿Eliminar esta propiedad? Esta acción no se puede deshacer.">
              <input type="hidden" name="csrf_token" value="<?php echo s(obtenerTokenCsrf()); ?>">
              <input type="hidden" name="id" value="<?php echo s($propiedad->id); ?>">
              <input type="hidden" name="tipo" value="propiedad">
              <input type="submit" class="boton-rojo-block" value="Eliminar">
            </form>
            <a href="/propiedades/actualizar?id=<?php echo rawurlencode((string) (int) $propiedad->id); ?>" class="boton-azul-block">Actualizar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h2>Vendedores</h2>
  <table class="propiedades">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Telefono</th>
        <th>Acciones</th>
      </tr>
    </thead>

    <tbody> <!-- Mostar Los Resultados -->
      <?php foreach ($vendedores as $vendedor): ?>
        <tr>
          <td><?php echo s($vendedor->id); ?></td>
          <td><?php echo s($vendedor->nombre) . " " . s($vendedor->apellido); ?></td>
          <td><?php echo s($vendedor->telefono); ?></td>
          <td>
            <form method="POST" class="w-100" action="/vendedores/eliminar" data-confirmar-eliminacion data-confirmacion="¿Eliminar este vendedor? Esta acción no se puede deshacer.">
              <input type="hidden" name="csrf_token" value="<?php echo s(obtenerTokenCsrf()); ?>">
              <input type="hidden" name="id" value="<?php echo s($vendedor->id); ?>">
              <input type="hidden" name="tipo" value="vendedor">
              <input type="submit" class="boton-rojo-block" value="Eliminar">
            </form>
            <a href="/vendedores/actualizar?id=<?php echo rawurlencode((string) (int) $vendedor->id); ?>" class="boton-azul-block">Actualizar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

<dialog class="dialogo-confirmacion" id="dialogo-confirmacion">
  <p id="dialogo-confirmacion-mensaje">¿Confirmás la eliminación?</p>
  <div class="dialogo-confirmacion__acciones">
    <button type="button" class="boton boton-verde" data-cancelar-eliminacion>Cancelar</button>
    <button type="button" class="boton-rojo-block" data-confirmar-eliminacion>Eliminar</button>
  </div>
</dialog>
