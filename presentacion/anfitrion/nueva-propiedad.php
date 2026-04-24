<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Propiedad | Estancias Digitales</title>
    <meta name="description" content="Registra una nueva propiedad en tu panel de anfitrión de Estancias Digitales.">
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/nueva-propiedad.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="host-body">

    <div class="host-wrapper">

        <!-- ═══════════ SIDEBAR ═══════════ -->
        <aside class="sidebar-host">
            <div class="host-logo-box">
                <h2>
                    <i class="fa-solid fa-house-laptop"></i>
                    Estancias Digitales
                </h2>
                <p>Modo Anfitrión</p>
            </div>

            <nav class="side-nav-host">
                <li class="side-nav-item" onclick="window.location.href='dashboard.php'">
                    <i class="fa-solid fa-house"></i> Inicio
                </li>
                <li class="side-nav-item active" onclick="window.location.href='propiedades.php'">
                    <i class="fa-solid fa-building"></i> Propiedades
                </li>
                <li class="side-nav-item" onclick="window.location.href='calendario.php'">
                    <i class="fa-solid fa-calendar-days"></i> Calendario
                </li>
                <li class="side-nav-item" onclick="window.location.href='reservas.php'">
                    <i class="fa-solid fa-receipt"></i> Reservas
                </li>
            </nav>

            
        </aside>

        <!-- ═══════════ CONTENIDO PRINCIPAL ═══════════ -->
        <main class="host-content-main">

            <!-- Top bar -->
            <header class="np-topbar">
                <div class="np-topbar-left">
                    <a href="propiedades.php" class="np-back-btn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <div>
                        <h1 class="np-page-title">Registrar nueva propiedad</h1>
                        <p class="np-page-sub">Completa la información de tu estancia para publicarla</p>
                    </div>
                </div>
            </header>

            <!-- Cuerpo del formulario -->
            <div class="np-body">
                <form id="formNuevaPropiedad" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="idUsuario" value="2">

                    <!-- ── SECCIÓN 1: INFORMACIÓN BÁSICA ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-blue">
                                <i class="fa-solid fa-circle-info"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Información básica</h2>
                                <p class="np-section-desc">Datos principales que verán los huéspedes</p>
                            </div>
                        </div>

                        <div class="np-form-group np-span-full">
                            <label class="np-label" for="nombre">Nombre de la propiedad <span class="np-required">*</span></label>
                            <input id="nombre" type="text" name="nombre" class="np-input"
                                placeholder="Ej: Villa Serena con Vista al Mar" required>
                        </div>

                        <div class="np-grid-2">
                            <div class="np-form-group">
                                <label class="np-label" for="idTipoPropiedad">Tipo de estancia <span class="np-required">*</span></label>
                                <div class="np-select-wrapper">
                                    <select id="idTipoPropiedad" name="idTipoPropiedad" class="np-select" required>
                                        <option value="">Selecciona tipo...</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down np-select-arrow"></i>
                                </div>
                            </div>

                            <div class="np-form-group">
                                <label class="np-label" for="precioNoche">Precio por noche <span class="np-required">*</span></label>
                                <div class="np-input-icon-wrap">
                                    <span class="np-input-prefix"><i class="fa-solid fa-euro-sign"></i></span>
                                    <input id="precioNoche" type="number" name="precioNoche" class="np-input np-input-prefixed"
                                        step="0.01" placeholder="0.00" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="np-grid-3">
                            <div class="np-form-group">
                                <label class="np-label" for="capacidadHuespedes">
                                    <i class="fa-solid fa-users np-label-icon"></i> Huéspedes <span class="np-required">*</span>
                                </label>
                                <input id="capacidadHuespedes" type="number" name="capacidadHuespedes"
                                    class="np-input np-input-center" value="1" min="1" required>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label" for="numeroHabitaciones">
                                    <i class="fa-solid fa-bed np-label-icon"></i> Habitaciones <span class="np-required">*</span>
                                </label>
                                <input id="numeroHabitaciones" type="number" name="numeroHabitaciones"
                                    class="np-input np-input-center" value="1" min="1" required>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label" for="numeroBanos">
                                    <i class="fa-solid fa-shower np-label-icon"></i> Baños
                                </label>
                                <input id="numeroBanos" type="number" name="numeroBanos"
                                    class="np-input np-input-center" value="1" min="1">
                            </div>
                        </div>
                    </div>

                    <!-- ── SECCIÓN 2: UBICACIÓN ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-green">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Ubicación</h2>
                                <p class="np-section-desc">¿Dónde se encuentra tu propiedad?</p>
                            </div>
                        </div>

                        <div class="np-grid-3">
                            <div class="np-form-group">
                                <label class="np-label" for="idPais">País <span class="np-required">*</span></label>
                                <div class="np-select-wrapper">
                                    <select id="idPais" class="np-select" required>
                                        <option value="">Selecciona país...</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down np-select-arrow"></i>
                                </div>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label" for="idEstado">Estado / Provincia <span class="np-required">*</span></label>
                                <div class="np-select-wrapper">
                                    <select id="idEstado" class="np-select" required disabled>
                                        <option value="">Selecciona estado...</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down np-select-arrow"></i>
                                </div>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label" for="idCiudad">Ciudad <span class="np-required">*</span></label>
                                <div class="np-select-wrapper">
                                    <select id="idCiudad" name="idCiudad" class="np-select" required disabled>
                                        <option value="">Selecciona ciudad...</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down np-select-arrow"></i>
                                </div>
                            </div>
                        </div>

                        <div class="np-form-group np-span-full">
                            <label class="np-label" for="direccion">Dirección exacta <span class="np-required">*</span></label>
                            <textarea id="direccion" name="direccion" class="np-textarea" rows="3"
                                placeholder="Calle, número, piso, código postal..." required></textarea>
                            <span class="np-hint"><i class="fa-solid fa-circle-exclamation"></i> La dirección solo se comparte con huéspedes confirmados.</span>
                        </div>
                    </div>

                    <!-- ── SECCIÓN 3: DESCRIPCIÓN ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-amber">
                                <i class="fa-solid fa-pen-nib"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Descripción</h2>
                                <p class="np-section-desc">Cuéntale a los huéspedes qué hace especial tu propiedad</p>
                            </div>
                        </div>

                        <div class="np-form-group np-span-full">
                            <label class="np-label" for="descripcion">Resumen general</label>
                            <textarea id="descripcion" name="descripcion" class="np-textarea" rows="5"
                                placeholder="Describe la experiencia que ofrecerás: ambiente, entorno, comodidades destacadas..."></textarea>
                        </div>
                    </div>

                    <!-- ── SECCIÓN 4: IMÁGENES ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-purple">
                                <i class="fa-solid fa-images"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Imágenes de la propiedad</h2>
                                <p class="np-section-desc">Las fotos generan hasta un 40% más de reservas</p>
                            </div>
                        </div>

                        <div class="np-upload-area" id="uploadArea">
                            <div class="np-upload-icon-ring">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <h3 class="np-upload-title">Arrastra tus fotos aquí</h3>
                            <p class="np-upload-sub">o haz clic para explorar tus archivos · JPG, PNG hasta 10 MB</p>
                            <span class="np-upload-badge">Seleccionar archivos</span>
                            <input type="file" name="imagenes[]" id="imagenes" multiple accept=".jpg,.jpeg,.png" style="display:none;">
                        </div>

                        <div class="np-preview-grid" id="previewContainer"></div>
                    </div>

                    <!-- ── SECCIÓN 5: SERVICIOS ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-teal">
                                <i class="fa-solid fa-list-check"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Servicios disponibles</h2>
                                <p class="np-section-desc">Selecciona los amenities que ofrece la propiedad</p>
                            </div>
                        </div>
                        <div class="np-check-grid" id="contenedorServicios">
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                        </div>
                    </div>

                    <!-- ── SECCIÓN 6: REGLAS ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-red">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Reglas de la propiedad</h2>
                                <p class="np-section-desc">Define las normas que deben respetar tus huéspedes</p>
                            </div>
                        </div>
                        <div class="np-check-grid" id="contenedorReglas">
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                        </div>
                        <div class="np-custom-rule-box">
                            <label class="np-label" for="reglaExtra">
                                <i class="fa-solid fa-plus-circle np-label-icon"></i>
                                ¿No encuentras tu regla? Agrégala manualmente
                            </label>
                            <input id="reglaExtra" type="text" name="reglaExtra" class="np-input"
                                placeholder="Ej: Prohibido hacer fiestas después de las 11 PM">
                        </div>
                    </div>

                    <!-- ── SECCIÓN 7: POLÍTICAS ── -->
                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-indigo">
                                <i class="fa-solid fa-file-contract"></i>
                            </span>
                            <div>
                                <h2 class="np-section-title">Políticas</h2>
                                <p class="np-section-desc">Condiciones de reserva y cancelación</p>
                            </div>
                        </div>
                        <div class="np-check-grid" id="contenedorPoliticas">
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                            <div class="np-check-skeleton"></div>
                        </div>
                    </div>

                    <!-- ── ACCIONES FINALES ── -->
                    <div class="np-form-actions">
                        <button type="button" class="np-btn-cancel" onclick="window.location.href='propiedades.php'">
                            <i class="fa-solid fa-xmark"></i> Cancelar
                        </button>
                        <button type="submit" class="np-btn-save" id="btnGuardar">
                            <i class="fa-solid fa-shield-check"></i> Publicar propiedad
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script src="../../recursos/js/anfitrion/nueva-propiedad.js"></script>
</body>
</html>