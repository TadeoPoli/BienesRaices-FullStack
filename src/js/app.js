document.addEventListener('DOMContentLoaded', function () {

    eventListener();

    darkMode();

    confirmarEliminacion();
});

function darkMode() {

    const prefiereDarkMode = window.matchMedia('(prefers-color-schema: dark)');

    //console.log(prefiereDarkMode.matches)

    if (prefiereDarkMode) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }

    prefiereDarkMode.addEventListener('change', function () {
        if (prefiereDarkMode) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    });

    const botonDarkMode = document.querySelector('.dark-mode-boton')

    botonDarkMode.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
    });

}


function eventListener() {
    const mobileMenu = document.querySelector('.mobile-menu');

    mobileMenu.addEventListener('click', navegacionResponsive)

    // Muestra campos Condiconales
    const metodoContacto = document.querySelectorAll('input[name="contacto[contacto]"]');
    metodoContacto.forEach(input => input.addEventListener('click', mostrarMetodosContacto));

}

function navegacionResponsive() {
    const navegacion = document.querySelector('.navegacion');

    navegacion.classList.toggle('mostrar');
}

function mostrarMetodosContacto(e) {
    const contactoDiv = document.querySelector('#contacto');
    if (e.target.value === 'telefono') {
        contactoDiv.innerHTML = `
            <label for= "telefono"> </label>
            <input type="tel" placeholder="Tu Telefono" id="telefono" name="contacto[telefono]"></input>

            <p>Si Elija la fecha y hora para la llamada</p>

            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="contacto[fecha]">

            <label for="hora">Hora</label>
            <input type="time" id="hora" min="08:00" max="20:00" name="contacto[hora]">

        `;
    } else {
        contactoDiv.innerHTML = `
            <label for="email"></label>
            <input type="email" placeholder="Tu Email" id="email" name="contacto[email]" require>
        `;
    }

}

function confirmarEliminacion() {
    const dialogo = document.querySelector('#dialogo-confirmacion');
    const formularios = document.querySelectorAll('form[data-confirmar-eliminacion]');

    if (!dialogo || !formularios.length) return;

    const mensaje = document.querySelector('#dialogo-confirmacion-mensaje');
    const botonCancelar = document.querySelector('[data-cancelar-eliminacion]');
    const botonConfirmar = dialogo.querySelector('[data-confirmar-eliminacion]');
    let formularioActivo = null;

    formularios.forEach(formulario => {
        formulario.addEventListener('submit', event => {
            if (formulario.dataset.confirmado === 'true') {
                delete formulario.dataset.confirmado;
                return;
            }

            event.preventDefault();
            formularioActivo = formulario;
            mensaje.textContent = formulario.dataset.confirmacion;
            dialogo.showModal();
        });
    });

    botonCancelar.addEventListener('click', () => {
        formularioActivo = null;
        dialogo.close();
    });

    botonConfirmar.addEventListener('click', () => {
        if (!formularioActivo) return;

        formularioActivo.dataset.confirmado = 'true';
        dialogo.close();
        formularioActivo.requestSubmit();
    });

    dialogo.addEventListener('cancel', event => {
        event.preventDefault();
        formularioActivo = null;
        dialogo.close();
    });
}
