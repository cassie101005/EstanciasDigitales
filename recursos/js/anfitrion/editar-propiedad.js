document.addEventListener('DOMContentLoaded', async () => {
    const idPropiedad = document.querySelector('input[name="idPropiedad"]').value;
    const form = document.getElementById('formEditarPropiedad');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('imagenes');
    const previewContainer = document.getElementById('previewContainer');
    const imagenesActualesDiv = document.getElementById('imagenesActuales');

    // 1. Cargar Catálogos
    await Promise.all([
        cargarTipos(),
        cargarPaises(),
        cargarServicios(),
        cargarReglas(),
        cargarPoliticas()
    ]);

    // 2. Cargar Datos de la Propiedad
    const res = await fetch(`../../apis/anfitrion/editar_propiedad.php?accion=obtener&id=${idPropiedad}`);
    const data = await res.json();

    if (data.ok) {
        const p = data.propiedad;
        document.getElementById('nombre').value = p.vNombre;
        document.getElementById('idTipoPropiedad').value = p.idTipoPropiedad;
        document.getElementById('precioNoche').value = p.dPrecioNoche;
        document.getElementById('capacidadHuespedes').value = p.iCapacidadHuespedes;
        document.getElementById('numeroHabitaciones').value = p.iNumeroHabitaciones;
        document.getElementById('numeroBanos').value = p.iNumeroBanos;
        document.getElementById('direccion').value = p.vDireccion;
        document.getElementById('descripcion').value = p.vDescripcion || '';
        document.getElementById('especificaciones').value = p.vEspecificaciones || '';

        // Ubicación
        document.getElementById('idPais').value = p.idPais;
        await cargarEstados(p.idPais);
        document.getElementById('idEstado').value = p.idEstado;
        await cargarCiudades(p.idEstado);
        document.getElementById('idCiudad').value = p.idCiudad;

        // Checkboxes
        data.servicios.forEach(id => {
            const cb = document.querySelector(`input[name="servicios[]"][value="${id}"]`);
            if(cb) cb.checked = true;
        });
        data.reglas.forEach(id => {
            const cb = document.querySelector(`input[name="reglas[]"][value="${id}"]`);
            if(cb) cb.checked = true;
        });
        data.politicas.forEach(id => {
            const cb = document.querySelector(`input[name="politicas[]"][value="${id}"]`);
            if(cb) cb.checked = true;
        });

        // Imágenes actuales
        renderImagenesActuales(data.imagenes);
    }

    // --- Manejo de Imágenes ---
    uploadArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        previewContainer.innerHTML = '';
        Array.from(fileInput.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'np-preview-item';
                div.innerHTML = `<img src="${e.target.result}">`;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    function renderImagenesActuales(imgs) {
        imagenesActualesDiv.innerHTML = '';
        imgs.forEach(img => {
            const div = document.createElement('div');
            div.className = 'img-edit-card';
            div.innerHTML = `
                <img src="../../${img.vImagen.replace(/ /g, '%20')}">
                <button type="button" class="btn-delete-img" onclick="eliminarImagen(${img.idImagen}, this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            imagenesActualesDiv.appendChild(div);
        });
    }

    window.eliminarImagen = async (idImg, btn) => {
        if(!confirm('¿Eliminar esta imagen?')) return;
        const fd = new FormData();
        fd.append('idImagen', idImg);
        fd.append('idPropiedad', idPropiedad);
        const r = await fetch('../../apis/anfitrion/editar_propiedad.php?accion=eliminar_imagen', { method: 'POST', body: fd });
        const d = await r.json();
        if(d.ok) btn.closest('.img-edit-card').remove();
    };

    // --- Envío de Formulario ---
    form.onsubmit = async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;
        btn.innerText = 'Guardando...';

        const formData = new FormData(form);
        
        // Convertir checkboxes a JSON string
        const servs = Array.from(document.querySelectorAll('input[name="servicios[]"]:checked')).map(c => c.value);
        const reglas = Array.from(document.querySelectorAll('input[name="reglas[]"]:checked')).map(c => c.value);
        const pols = Array.from(document.querySelectorAll('input[name="politicas[]"]:checked')).map(c => c.value);
        
        formData.set('servicios', JSON.stringify(servs));
        formData.set('reglas', JSON.stringify(reglas));
        formData.set('politicas', JSON.stringify(pols));

        try {
            // 1. Actualizar datos
            const res = await fetch('../../apis/anfitrion/editar_propiedad.php?accion=actualizar', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.ok) {
                // 2. Subir nuevas imágenes si hay
                if (fileInput.files.length > 0) {
                    const fdImgs = new FormData();
                    fdImgs.append('idPropiedad', idPropiedad);
                    for (let i = 0; i < fileInput.files.length; i++) {
                        fdImgs.append('imagenes[]', fileInput.files[i]);
                    }
                    await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=subir_imagenes', {
                        method: 'POST',
                        body: fdImgs
                    });
                }
                alert('Propiedad actualizada con éxito');
                window.location.href = 'propiedades.php';
            } else {
                alert(data.error || 'Error al actualizar');
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Guardar Cambios';
        }
    };

    // --- Funciones de Carga de Catálogos (Similares a nueva-propiedad.js) ---
    async function cargarTipos() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=tipos');
        const d = await r.json();
        const sel = document.getElementById('idTipoPropiedad');
        sel.innerHTML = '<option value="">Selecciona tipo...</option>';
        d.tipos.forEach(t => sel.innerHTML += `<option value="${t.idTipoPropiedad}">${t.vNombreCategoria}</option>`);
    }

    async function cargarPaises() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=paises');
        const d = await r.json();
        const sel = document.getElementById('idPais');
        sel.innerHTML = '<option value="">Selecciona país...</option>';
        d.paises.forEach(p => sel.innerHTML += `<option value="${p.idPais}">${p.vNombrePais}</option>`);
        
        sel.onchange = () => cargarEstados(sel.value);
    }

    async function cargarEstados(idPais) {
        const r = await fetch(`../../apis/anfitrion/registrar_propiedad.php?accion=estados&idPais=${idPais}`);
        const d = await r.json();
        const sel = document.getElementById('idEstado');
        sel.innerHTML = '<option value="">Selecciona estado...</option>';
        d.estados.forEach(e => sel.innerHTML += `<option value="${e.idEstado}">${e.vNombreEstado}</option>`);
        
        sel.onchange = () => cargarCiudades(sel.value);
    }

    async function cargarCiudades(idEstado) {
        const r = await fetch(`../../apis/anfitrion/registrar_propiedad.php?accion=ciudades&idEstado=${idEstado}`);
        const d = await r.json();
        const sel = document.getElementById('idCiudad');
        sel.innerHTML = '<option value="">Selecciona ciudad...</option>';
        d.ciudades.forEach(c => sel.innerHTML += `<option value="${c.idCiudad}">${c.vNombreCiudad}</option>`);
    }

    async function cargarServicios() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=servicios');
        const d = await r.json();
        const cont = document.getElementById('contenedorServicios');
        cont.innerHTML = '<h4>Servicios</h4>';
        d.servicios.forEach(s => {
            cont.innerHTML += `<label class="np-check-item"><input type="checkbox" name="servicios[]" value="${s.idServicio}"> ${s.vNombreServicio}</label>`;
        });
    }

    async function cargarReglas() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=reglas');
        const d = await r.json();
        const cont = document.getElementById('contenedorReglas');
        cont.innerHTML = '<h4>Reglas</h4>';
        d.reglas.forEach(r => {
            cont.innerHTML += `<label class="np-check-item"><input type="checkbox" name="reglas[]" value="${r.idRegla}"> ${r.vNombreRegla}</label>`;
        });
    }

    async function cargarPoliticas() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=politicas');
        const d = await r.json();
        const cont = document.getElementById('contenedorPoliticas');
        cont.innerHTML = '<h4>Políticas</h4>';
        d.politicas.forEach(p => {
            cont.innerHTML += `<label class="np-check-item"><input type="checkbox" name="politicas[]" value="${p.idPolitica}"> ${p.vNombrePol}</label>`;
        });
    }
});
