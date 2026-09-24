<main class="contenedor seccion contenido-centrado">
    <h1><?php echo s($propiedad->titulo); ?></h1>


    <img loading="lazy" src="<?php echo urlImagen($propiedad->imagen); ?>" alt="imagen propiedad">


    <div class="resumen-propiedad">
        <p class="precio">$<?php echo s($propiedad->precio); ?></p>
        <ul class="iconos-caracteristicas">
            <li>
                <img img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="iconowc">
                <p><?php echo s($propiedad->wc); ?></p>
            </li>
            <li>
                <img img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="iconoestacionamiento">
                <p><?php echo s($propiedad->estacionamientos); ?></p>
            </li>
            <li>
                <img img class="icono" loading="lazy" src="build/img/icono_dormitorio.svg" alt="iconoabitaciones">
                <p><?php echo s($propiedad->habitaciones); ?></p>
            </li>
        </ul>

        <p class="descripcion-completa"><?php echo s($propiedad->descripcion); ?></p>

    </div>

</main>
