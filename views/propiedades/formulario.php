<input type="hidden" name="csrf_token" value="<?php echo s(obtenerTokenCsrf()); ?>">

<fieldset>
            <legend>Informacion General</legend>

            <label for="titulo">Titulo</label>
            <input type="text" id="titulo" name="propiedad[titulo]" placeholder="Titulo Propiedad" maxlength="255" required value="<?php echo s($propiedad->titulo); ?>">

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="propiedad[precio]" placeholder="Precio Propiedad" min="0.01" max="9999999999.99" step="0.01" required value="<?php echo s($propiedad->precio); ?>">

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" accept="image/jpeg,image/png,image/webp" name="propiedad[imagen]" <?php echo !$propiedad->imagen ? 'required' : ''; ?>>

            <?php if($propiedad->imagen) { ?>
                <img src="<?php echo urlImagen($propiedad->imagen); ?>" class="imagen-small" alt="">
            <?php } ?>

            <label for="descripcion">Descripcion</label>
            <textarea id="descripcion" name="propiedad[descripcion]" minlength="50" maxlength="5000" required><?php echo s($propiedad->descripcion); ?></textarea>
        </fieldset>

        <fieldset>
            <legend>Informacion Propiedad</legend>

            <label for="habitaciones">Habitaciones</label>
            <input type="number" id="habitaciones" name="propiedad[habitaciones]" placeholder="Ej: 3" min="0" max="255" required value="<?php echo s($propiedad->habitaciones); ?>">

            <label for="wc">Baños</label>
            <input type="number" id="wc" name="propiedad[wc]" placeholder="Ej: 3" min="0" max="255" required value="<?php echo s($propiedad->wc); ?>">

            <label for="estacionamientos">Estacionamientos</label>
            <input type="number" id="estacionamientos" name="propiedad[estacionamientos]" placeholder="Ej: 3" min="0" max="255" required value="<?php echo s($propiedad->estacionamientos); ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>
            <label for="vendedor">Vendedores</label>

            <select name="propiedad[vendedores_id]" id="vendedor" required>
                <option value="">-- Seleccione --</option>
                <?php foreach($vendedores as $vendedor): ?>
                    <option
                    <?php echo $propiedad->vendedores_id === $vendedor->id ? 'selected' : ''; ?>
                    value="<?php echo s($vendedor->id); ?>"><?php echo s($vendedor->nombre) . " " . s($vendedor->apellido); ?></option>

                <?php endforeach; ?>
            </select>
        </fieldset>
