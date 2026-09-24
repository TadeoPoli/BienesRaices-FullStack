<input type="hidden" name="csrf_token" value="<?php echo s(obtenerTokenCsrf()); ?>">

<fieldset>
            <legend>Informacion General</legend>

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="vendedor[nombre]" placeholder="Nombre Vendedor" maxlength="100" required value="<?php echo s($vendedor->nombre); ?>">

            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="vendedor[apellido]" placeholder="Apellido Vendedor" maxlength="100" required value="<?php echo s($vendedor->apellido); ?>">
</fieldset>

<fieldset>
    <legend>Informacion Extra</legend>

    <label for="telefono">Telefono</label>
    <input type="text" id="telefono" name="vendedor[telefono]" placeholder="Telefono Vendedor" maxlength="20" required value="<?php echo s($vendedor->telefono); ?>">
</fieldset>
