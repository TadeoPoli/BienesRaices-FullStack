 <main class="contenedor seccion contenido-centrado">
     <h1>Iniciar Sesion</h1>

     <?php foreach ($errores as $error): ?>
         <div class="alerta error">
             <?php echo s($error); ?>
         </div>
     <?php endforeach; ?>

     <form method="POST" class="formulario" action="/login">
         <input type="hidden" name="csrf_token" value="<?php echo s(obtenerTokenCsrf()); ?>">
         <fieldset>
             <legend>Email y Password</legend>

             <label for="email">Email</label>
             <input type="email" name="email" placeholder="Tu Email" id="email" maxlength="255" required>

             <label for="password">Password</label>
             <input type="password" name="password" placeholder="Tu Password" id="password" maxlength="255" required>

         </fieldset>

         <input type="submit" value="Iniciar Sesion" class="boton boton-verde">
     </form>
 </main>
