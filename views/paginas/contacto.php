<main class="contenedor seccion">
    <h1>Contacto</h1>

    <?php
    if($mensaje) { ?>
        <p class="alerta <?php echo ($tipoMensaje ?? '') === 'error' ? 'error' : 'exito'; ?>"><?php echo s($mensaje); ?></p>
    <?php } ?>


    <picture>
        <source srcset="build/img/destacada3.webp" type="image/webp">
        <source srcset="build/img/destacada3.jpg" type="image/jepg">
        <img loading="lazy" src="build/img/destacada3.jpg" alt="Contactos">
    </picture>

    <h2>Llene el Formulario de Contacto</h2>

    <form class="formulario" action="/contacto" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo s(obtenerTokenCsrf()); ?>">
        <fieldset>
            <legend>Informacion Personal</legend>

            <label for="nombre">Nombre</label>
            <input type="text" placeholder="Tu Nombre" id="nombre" name="contacto[nombre]" maxlength="100" required>

            <label for="mensaje">Mensaje</label>
            <textarea id="mensaje" name="contacto[mensaje]" minlength="10" maxlength="2000" required></textarea>
        </fieldset>

        <fieldset>
            <legend>Informacion Sobre la Propiedad</legend>

            <label for="opciones">Vende o Compra</label>
            <select id="opciones" name="contacto[tipo]" required>
                <option value="" disabled select>--Seleccione--</option>
                <option value="compra">Compra</option>
                <option value="vende">Vende</option>
            </select>

            <label for="presupuesto">Precio o Presupuesto</label>
            <input type="number" placeholder="Tu Presupuesto" id="presupuesto" name="contacto[precio]" min="0.01" max="9999999999.99" step="0.01" required>

        </fieldset>

        <fieldset>
            <legend>Contacto</legend>

            <p>Como Desea ser Contactado</p>

            <div class="forma-contacto">
                <label for="contactar-telefono">Telefono</label>
                <input  type="radio" value="telefono" id="contactar-telefono" name="contacto[contacto]" required>

                <label for="contactar-email">Email</label>
                <input  type="radio" value="email" id="contactar-email" name="contacto[contacto]" required>
            </div>

            <div id="contacto"></div>


        </fieldset>

        <input type="submit" value="enviar" class="boton-verde-contacto">
    </form>
</main>
