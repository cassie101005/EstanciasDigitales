// ── Límite y acumulador global de imágenes ──
const MAX_IMAGENES = 10;
let archivosImagenes = []; // Array acumulativo de File objects

document.addEventListener('DOMContentLoaded', () => {
    cargarTiposPropiedad();
    cargarPaises();
    cargarServicios();
    cargarReglas();
    cargarPoliticas();

    // Eventos de cambios en combo boxes
    document.getElementById('idPais').addEventListener('change', async (e) => {
        const idPais = e.target.value;
        const estadoSelect = document.getElementById('idEstado');
        const ciudadSelect = document.getElementById('idCiudad');
        
        estadoSelect.innerHTML = '<option value="">Selecciona estado</option>';
        ciudadSelect.innerHTML = '<option value="">Selecciona ciudad</option>';
        estadoSelect.disabled = true;
        ciudadSelect.disabled = true;

        if (idPais) {
            try {
                const res = await fetch(`../../apis/anfitrion/registrar_propiedad.php?accion=estados&idPais=${idPais}`);
                const data = await res.json();
                if (data.ok) {
                    data.estados.forEach(est => {
                        estadoSelect.innerHTML += `<option value="${est.idEstado}">${est.vNombreEstado}</option>`;
                    });
                    estadoSelect.disabled = false;
                }
            } catch (error) {
                console.error("Error al cargar estados", error);
            }
        }
    });

    document.getElementById('idEstado').addEventListener('change', async (e) => {
        const idEstado = e.target.value;
        const ciudadSelect = document.getElementById('idCiudad');
        
        ciudadSelect.innerHTML = '<option value="">Selecciona ciudad</option>';
        ciudadSelect.disabled = true;

        if (idEstado) {
            try {
                const res = await fetch(`../../apis/anfitrion/registrar_propiedad.php?accion=ciudades&idEstado=${idEstado}`);
                const data = await res.json();
                if (data.ok) {
                    data.ciudades.forEach(ciu => {
                        ciudadSelect.innerHTML += `<option value="${ciu.idCiudad}">${ciu.vNombreCiudad}</option>`;
                    });
                    ciudadSelect.disabled = false;
                }
            } catch (error) {
                console.error("Error al cargar ciudades", error);
            }
        }
    });

    // Configurar Drag & Drop de imágenes
    configurarDragAndDrop();

    // Enviar el formulario
    document.getElementById('formNuevaPropiedad').addEventListener('submit', guardarPropiedad);
});

// Cargas iniciales
async function cargarTiposPropiedad() {
    try {
        const res = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=tipos');
        const data = await res.json();
        if (data.ok) {
            const select = document.getElementById('idTipoPropiedad');
            data.tipos.forEach(tipo => {
                select.innerHTML += `<option value="${tipo.idTipoPropiedad}">${tipo.vNombreCategoria}</option>`;
            });
        }
    } catch (e) {
        console.error("Error cargando tipos", e);
    }
}

async function cargarPaises() {
    try {
        const res = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=paises');
        const data = await res.json();
        if (data.ok) {
            const select = document.getElementById('idPais');
            data.paises.forEach(pais => {
                select.innerHTML += `<option value="${pais.idPais}">${pais.vNombrePais}</option>`;
            });
        }
    } catch (e) {
        console.error("Error cargando paises", e);
    }
}

async function cargarServicios() {
    try {
        const res = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=servicios');
        const data = await res.json();
        if (data.ok) {
            const container = document.getElementById('contenedorServicios');
            container.innerHTML = '';
            data.servicios.forEach(s => {
                container.innerHTML += `
                    <label class="checkbox-group">
                        <input type="checkbox" name="chkServicio" value="${s.idServicio}"> ${s.vNombreServicio}
                    </label>
                `;
            });
        }
    } catch (e) {
        console.error("Error cargando servicios", e);
    }
}

async function cargarReglas() {
    try {
        const res = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=reglas');
        const data = await res.json();
        if (data.ok) {
            const container = document.getElementById('contenedorReglas');
            container.innerHTML = '';
            data.reglas.forEach(r => {
                container.innerHTML += `
                    <label class="checkbox-group">
                        <input type="checkbox" name="chkRegla" value="${r.idRegla}"> ${r.vNombreRegla}
                    </label>
                `;
            });
        }
    } catch (e) {
        console.error("Error cargando reglas", e);
    }
}

async function cargarPoliticas() {
    try {
        const res = await fetch('../../apis/anfitrion/registrar_propiedad.php?accion=politicas');
        const data = await res.json();
        if (data.ok) {
            const container = document.getElementById('contenedorPoliticas');
            container.innerHTML = '';
            data.politicas.forEach(p => {
                container.innerHTML += `
                    <label class="checkbox-group">
                        <input type="checkbox" name="chkPolitica" value="${p.idPolitica}"> ${p.vNombrePol}
                    </label>
                `;
            });
        }
    } catch (e) {
        console.error("Error cargando politicas", e);
    }
}

async function guardarPropiedad(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
    btn.disabled = true;

    // Recolectar datos del formulario
    const formData = new FormData(e.target);
    
    // Recolectar checkboxes (Servicios, Reglas, Politicas)
    const servicios = Array.from(document.querySelectorAll('input[name="chkServicio"]:checked')).map(cb => cb.value);
    const reglas = Array.from(document.querySelectorAll('input[name="chkRegla"]:checked')).map(cb => cb.value);
    const politicas = Array.from(document.querySelectorAll('input[name="chkPolitica"]:checked')).map(cb => cb.value);
    
    formData.append('accion', 'guardar');
    formData.append('servicios', JSON.stringify(servicios));
    formData.append('reglas', JSON.stringify(reglas));
    formData.append('politicas', JSON.stringify(politicas));

    try {
        const res = await fetch('../../apis/anfitrion/registrar_propiedad.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        
        if (data.ok) {
            const idPropiedad = data.idPropiedad;

            if (archivosImagenes.length > 0) {
                const imgData = new FormData();
                imgData.append('accion', 'subir_imagenes');
                imgData.append('idPropiedad', idPropiedad);

                archivosImagenes.forEach(file => {
                    imgData.append('imagenes[]', file);
                });

                await fetch('../../apis/anfitrion/registrar_propiedad.php', {
                    method: 'POST',
                    body: imgData
                });
            }
            
            alert('¡Propiedad guardada correctamente!');
            e.target.reset(); // Limpia el formulario
            // También podemos redirigir a mis propiedades
            window.location.href = 'propiedades.php';
        } else {
            alert(data.error || data.mensaje || 'Hubo un error al registrar.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch (error) {
        console.error("Error registrando", error);
        alert('Problema al contactar al servidor.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// Funcionalidad Drag and Drop
function configurarDragAndDrop() {
    const uploadArea  = document.getElementById('uploadArea');
    const inputImagenes = document.getElementById('imagenes');
    const previewContainer = document.getElementById('previewContainer');

    // ── Actualiza el contador visible en el área de carga ──
    function actualizarContador() {
        let contador = uploadArea.querySelector('.np-img-counter');
        if (!contador) {
            contador = document.createElement('span');
            contador.className = 'np-img-counter';
            contador.style.cssText = 'display:block; font-size:12px; font-weight:700; color:#6B7280; margin-top:6px;';
            uploadArea.appendChild(contador);
        }
        if (archivosImagenes.length === 0) {
            contador.textContent = '';
        } else if (archivosImagenes.length >= MAX_IMAGENES) {
            contador.style.color = '#E11D48';
            contador.textContent = `Límite alcanzado: ${archivosImagenes.length}/${MAX_IMAGENES} fotos`;
        } else {
            contador.style.color = '#6B7280';
            contador.textContent = `${archivosImagenes.length}/${MAX_IMAGENES} fotos seleccionadas`;
        }
    }

    // ── Agrega nuevos archivos al acumulador respetando el límite ──
    function agregarArchivos(nuevosArchivos) {
        const disponibles = MAX_IMAGENES - archivosImagenes.length;

        if (disponibles <= 0) {
            mostrarAlertaLimite();
            return;
        }

        const validos = [...nuevosArchivos]
            .filter(f => f.type.startsWith('image/'));

        const aAgregar = validos.slice(0, disponibles);

        if (validos.length > disponibles) {
            mostrarAlertaLimite(validos.length - disponibles);
        }

        aAgregar.forEach(file => archivosImagenes.push(file));
        renderPreviews();
        actualizarContador();
    }

    // ── Alerta cuando se supera el límite ──
    function mostrarAlertaLimite(exceso = 0) {
        const msg = exceso > 0
            ? `Solo puedes adjuntar ${MAX_IMAGENES} imágenes por propiedad. Se descartaron ${exceso} foto(s).`
            : `Has alcanzado el límite de ${MAX_IMAGENES} imágenes por propiedad.`;
        // Muestra notificación inline en lugar de alert() del navegador
        let alerta = document.getElementById('npAlertaImagenes');
        if (!alerta) {
            alerta = document.createElement('div');
            alerta.id = 'npAlertaImagenes';
            alerta.style.cssText = [
                'display:flex; align-items:center; gap:10px;',
                'background:#FFF1F2; border:1px solid #FECDD3;',
                'color:#E11D48; font-size:13px; font-weight:600;',
                'padding:0.75rem 1.25rem; border-radius:12px; margin-top:1rem;'
            ].join('');
            previewContainer.parentNode.insertBefore(alerta, previewContainer);
        }
        alerta.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> ${msg}`;
        alerta.style.display = 'flex';
        clearTimeout(alerta._timer);
        alerta._timer = setTimeout(() => { alerta.style.display = 'none'; }, 4000);
    }

    // ── Renderiza todos los previews desde el array acumulador ──
    function renderPreviews() {
        previewContainer.innerHTML = '';
        archivosImagenes.forEach((file, idx) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function () {
                const div = document.createElement('div');
                div.classList.add('img-thumb');
                div.style.position = 'relative';

                const img = document.createElement('img');
                img.src = reader.result;

                // Botón eliminar ──
                const btnEliminar = document.createElement('button');
                btnEliminar.type = 'button';
                btnEliminar.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                btnEliminar.style.cssText = [
                    'position:absolute; top:6px; right:6px;',
                    'width:24px; height:24px; border-radius:50%;',
                    'background:rgba(0,0,0,0.55); color:white; border:none;',
                    'cursor:pointer; font-size:11px; display:flex;',
                    'align-items:center; justify-content:center; z-index:2;'
                ].join('');
                btnEliminar.addEventListener('click', () => {
                    archivosImagenes.splice(idx, 1);
                    renderPreviews();
                    actualizarContador();
                });

                div.appendChild(img);
                div.appendChild(btnEliminar);
                previewContainer.appendChild(div);
            };
        });
    }

    // ── Click en el área abre el selector ──
    uploadArea.addEventListener('click', (e) => {
        if (e.target.closest('button')) return; // evita re-abrir al clicar eliminar
        if (archivosImagenes.length >= MAX_IMAGENES) {
            mostrarAlertaLimite();
            return;
        }
        inputImagenes.click();
    });

    // ── Evitar comportamientos por defecto del navegador ──
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
        document.body.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
    });

    // Efectos visuales al arrastrar
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            if (archivosImagenes.length < MAX_IMAGENES) {
                uploadArea.style.borderColor = '#7C3AED';
                uploadArea.style.background  = 'rgba(124, 58, 237, 0.04)';
            }
        }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.borderColor = '';
            uploadArea.style.background  = '';
        }, false);
    });

    // ── Drop ──
    uploadArea.addEventListener('drop', (e) => {
        agregarArchivos(e.dataTransfer.files);
    }, false);

    // ── Selección por click ──
    inputImagenes.addEventListener('change', function () {
        agregarArchivos(this.files);
        this.value = ''; // resetea el input para permitir re-selección
    });
}
