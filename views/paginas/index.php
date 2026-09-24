<main class="contenedor seccion">
    <h1>Mas Sobre Nosotros</h1>
    <?php include 'iconos.php' ?>
</main>

<section class="seccion-contenedor">
    <h2>Casas y Departamentos en Ventas</h2>

    <?php
    include 'listado.php';
    ?>

    <div class="alinear-derecha">
        <a href="/propiedades" class="boton-verde-anuncios">Ver Todas</a>
    </div>
</section>

<section class="imagen-contacto">
    <h2>Encuentra la casa de tus sueños</h2>
    <p>Llena el Formulario y uno de nuestros asesores se pondra en contacto con usted</p>
    <a href="/contacto" class="boton-amarillo">Contactanos</a>
</section>

<div class="contenedor seccion seccion-inferior">
    <section class="blog">
        <h3>Nuesto Blog</h3>

        <article class="entrada-blog">
            <div class="imagen">
                <picture>
                    <source srcset="build/img/blog1.webp" type="image/webp">
                    <source srcset="build/img/blog1.jpg" type="image/jepg">
                    <img loading="lazy" src="build/img/blog1.jpg" alt="texto entrada">
                </picture>
            </div>

            <div class="texto-entrada">
                <a href="/entrada">
                    <h4>Terraza en el techo de tu casa</h4>
                    <p>Escrito el: <span>20/10/2021</span> por: <span>Admin</span> </p>
                    <p class="informacion-meta">Consejo para construir una terraza en el techo de tu casa de forma
                        economica</p>
                </a>
            </div>
        </article>

        <article class="entrada-blog">
            <div class="imagen">
                <picture>
                    <source srcset="build/img/blog2.webp" type="image/webp">
                    <source srcset="build/img/blog2.jpg" type="image/jepg">
                    <img loading="lazy" src="build/img/blog2.jpg" alt="texto entrada">
                </picture>
            </div>

            <div class="texto-entrada">
                <a href="/entrada">
                    <h4>Guia para decorar tu hogar</h4>
                    <p class="informacion-meta">Escrito el: <span>20/10/2021</span> por: <span>Admin</span> </p>
                    <p>Maximiza el espacio de tu hogar con esta guia para aprender a decorar y acomodar tus muebles
                    </p>
                </a>
            </div>
        </article>
    </section>

    <section class="testimoniales">
        <h3>Testimoniales</h3>

        <div class="testimonial">
            <blockquote>
                El personal se comporto de una exelente forma y la atencion fue muy buena y la casa cumplio con
                todas mis espectativas y mas
            </blockquote>
            <p>Tadeo Polischuk</p>
        </div>
    </section>
</div>
