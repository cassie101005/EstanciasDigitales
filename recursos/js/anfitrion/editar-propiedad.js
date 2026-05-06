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
        document.getElementById('dTarifaLimpieza').value = p.dTarifaLimpieza;
        document.getElementById('capacidadHuespedes').value = p.iCapacidadHuespedes;
        document.getElementById('numeroHabitaciones').value = p.iNumeroHabitaciones;
        document.getElementById('numeroBanos').value = p.iNumeroBanos;
        document.getElementById('direccion').value = p.vDireccion || '';
        document.getElementById('descripcion').value = p.vDescripcion || '';

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
        fd.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        const r = await fetch('../../apis/anfitrion/editar_propiedad.php?accion=eliminar_imagen', { method: 'POST', body: fd });
        const d = await r.json();
        if(d.ok) btn.closest('.img-edit-card').remove();
    };

    // --- Protección contra cambios no guardados ---
    let formModificado = false;
    form.addEventListener('input', () => formModificado = true);
    form.addEventListener('change', () => formModificado = true);

    // Función para manejar navegación segura
    const navegacionSegura = (url) => {
        if (formModificado) {
            Swal.fire({
                title: '¿Deseas guardar los cambios?',
                text: "Tienes cambios sin guardar en tu propiedad.",
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Guardar ahora',
                denyButtonText: 'No guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: 'var(--primary)',
                denyButtonColor: '#64748b',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simular clic en guardar y luego navegar
                    form.requestSubmit();
                } else if (result.isDenied) {
                    formModificado = false;
                    window.location.href = url;
                }
            });
        } else {
            window.location.href = url;
        }
    };

    // Sobrescribir clics en el sidebar
    document.querySelectorAll('.side-nav-item').forEach(item => {
        const originalOnClick = item.getAttribute('onclick');
        if (originalOnClick && originalOnClick.includes('window.location.href')) {
            const url = originalOnClick.match(/'([^']+)'/)[1];
            item.removeAttribute('onclick');
            item.addEventListener('click', (e) => {
                e.preventDefault();
                navegacionSegura(url);
            });
        }
    });

    // Manejar botón Cancelar
    const btnCancelar = document.querySelector('.np-btn-cancel');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', (e) => {
            e.preventDefault();
            navegacionSegura('propiedades.php');
        });
    }

    // Browser level
    window.addEventListener('beforeunload', (e) => {
        if (formModificado) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

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
            const response = await fetch('../../apis/anfitrion/editar_propiedad.php?accion=actualizar', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("La respuesta del servidor no es un JSON válido.");
            }

            if (data.ok) {
                // Subir nuevas imágenes si hay
                if (fileInput.files.length > 0) {
                    const fdImgs = new FormData();
                    fdImgs.append('idPropiedad', idPropiedad);
                    fdImgs.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                    for (let i = 0; i < fileInput.files.length; i++) {
                        fdImgs.append('imagenes[]', fileInput.files[i]);
                    }
                    await fetch('../../apis/anfitrion/editar_propiedad.php?accion=subir_imagenes', {
                        method: 'POST',
                        body: fdImgs
                    });
                }
                
                formModificado = false; // Reset flag
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Propiedad actualizada correctamente',
                    icon: 'success',
                    confirmButtonColor: 'var(--primary)'
                }).then(() => {
                    window.location.href = 'propiedades.php';
                });

            } else {
                Swal.fire('Error', data.error || 'Error al actualizar', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Error de conexión', 'error');
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
        cont.innerHTML = '';
        d.servicios.forEach(s => {
            cont.innerHTML += `
                <label class="checkbox-group">
                    <input type="checkbox" name="servicios[]" value="${s.idServicio}"> ${s.vNombreServicio}
                </label>
            `;
        });
    }

    async function cargarReglas() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=reglas');
        const d = await r.json();
        const cont = document.getElementById('contenedorReglas');
        cont.innerHTML = '';
        d.reglas.forEach(r => {
            cont.innerHTML += `
                <label class="checkbox-group">
                    <input type="checkbox" name="reglas[]" value="${r.idRegla}"> ${r.vNombreRegla}
                </label>
            `;
        });
    }

    async function cargarPoliticas() {
        const r = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=politicas');
        const d = await r.json();
        const cont = document.getElementById('contenedorPoliticas');
        cont.innerHTML = '';
        d.politicas.forEach(p => {
            cont.innerHTML += `
                <label class="checkbox-group">
                    <input type="checkbox" name="politicas[]" value="${p.idPolitica}"> ${p.vNombrePol}
                </label>
            `;
        });
    }
});
