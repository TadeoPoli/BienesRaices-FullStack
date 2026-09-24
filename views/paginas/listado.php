<div class="contenedor-anuncios">
    <?php foreach ($propiedades as $propiedad) { ?>
        <div class="anuncios">

            <img loading="lazy" src="<?php echo urlImagen($propiedad->imagen); ?>" alt="anuncio">

            <div class="contenido-anuncio">
                <h3><?php echo s($propiedad->titulo); ?></h3>
                <p class="descripcion-corta"><?php echo s($propiedad->descripcion); ?></p>
                <p class="precio">$<?php echo s($propiedad->precio); ?></p>
                <ul class="iconos-caracteristicas">
                    <li>
                        <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="iconowc">
                        <p><?php echo s($propiedad->wc); ?></p>
                    </li>
                    <li>
                        <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="iconoestacionamiento">
                        <p><?php echo s($propiedad->estacionamientos); ?></p>
                    </li>
                    <li>
                        <img class="icono" loading="lazy" src="build/img/icono_dormitorio.svg" alt="iconoabitaciones">
                        <p><?php echo s($propiedad->habitaciones); ?></p>
                    </li>
                </ul>

                <a href="/propiedad?id=<?php echo rawurlencode((string) (int) $propiedad->id); ?>" class="boton-amarillo-block">
                    Ver Propiedad
                </a>
            </div><!--.contenido-anuncios-->
        </div><!--Anuncio-->
    <?php } ?>
</div>
