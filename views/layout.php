<?php
if(!isset($_SESSION)) {
    session_start();
}

$auth = $_SESSION['login'] ?? false;

if(!isset($inicio)) {
    $inicio = false;
}

$rutaActual = $_SERVER['PATH_INFO'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$titulos = [
    '/' => 'Bienes Raíces',
    '/nosotros' => 'Nosotros',
    '/propiedades' => 'Propiedades',
    '/propiedad' => 'Propiedad',
    '/blog' => 'Blog',
    '/entrada' => 'Entrada de Blog',
    '/contacto' => 'Contacto',
    '/login' => 'Iniciar Sesión',
    '/admin' => 'Administración',
    '/propiedades/crear' => 'Crear Propiedad',
    '/propiedades/actualizar' => 'Actualizar Propiedad',
    '/vendedores/crear' => 'Crear Vendedor',
    '/vendedores/actualizar' => 'Actualizar Vendedor'
];
$titulo = $titulos[$rutaActual] ?? 'Bienes Raíces';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo s($titulo); ?></title>
    <link rel="stylesheet" href="/build/css/app.css">
</head>
<body>
    <header class="header <?php echo $inicio ? 'inicio' : ''; ?>">
        <div class="contenedor contenido-header">
            <div class="barra">
                <a href="/">
                    <img src="/build/img/logo.svg" alt="Logotivo De Bienes Raices">
                </a>

               <div class="mobile-menu">
                    <img src="/build/img/barras.svg" alt="icono menu">
                </div>

                <div class="derecha">
                    <img class="dark-mode-boton" src="/build/img/dark-mode.svg" alt="dark mode">
                    <nav class="navegacion">
                        <a href="/nosotros">Nosotros</a>
                        <a href="/propiedades">Propiedades</a>
                        <a href="/blog">Blog</a>
                        <a href="/contacto">Contacto</a>
                        <?php if($auth): ?>
                            <a href="/admin">Administración</a>
                            <a href="/logout">Cerrar sesión</a>
                        <?php else: ?>
                            <a href="/login">Iniciar sesión</a>
                        <?php endif; ?>
                    </nav>
                </div>

            </div> <!--Cierre de la Barra-->

            <?php
              if($inicio){
                echo "<h1>Venta De Casas Y Departamentos Exclusivos de Lujo</h1>";
              }
            ?>

        </div>

    </header>

    <?php echo $contenido; ?>

    <footer class="footer seccion">
        <div class="contenedor contenido-footer">
            <nav class="navegacion">
                <a href="/nosotros">Nosotros</a>
                <a href="/propiedades">Anuncios</a>
                <a href="/blog">Blog</a>
                <a href="/contacto">Contacto</a>
            </nav>
        </div>

        <p class="copyright">Todos los Derechos Reservados <?php echo date('Y'); ?> &copy;</p>
    </footer>
    <script src="/build/js/bundle.min.js"></script>
</body>
</html>
