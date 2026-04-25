<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('anfitrion', '../../');
require_once '../../datos/conexion.php';
$idPropiedad = intval($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Propiedad | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/nueva-propiedad.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .current-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .img-edit-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
            border: 1px solid #e2e8f0;
        }
        .img-edit-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-delete-img {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: transform 0.2s;
        }
        .btn-delete-img:hover { transform: scale(1.1); }
    </style>
</head>
<body class="host-body">

    <div class="host-wrapper">
        <aside class="sidebar-host">
            <div class="host-logo-box">
                <h2><i class="fa-solid fa-house-laptop"></i> Estancias Digitales</h2>
                <p>Modo Anfitrión</p>
            </div>
            <nav class="side-nav-host">
                <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                <li class="side-nav-item active" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
            </nav>
        </aside>

        <main class="host-content-main">
            <header class="np-topbar">
                <div class="np-topbar-left">
                    <a href="propiedades.php" class="np-back-btn"><i class="fa-solid fa-chevron-left"></i></a>
                    <div>
                        <h1 class="np-page-title">Editar Propiedad</h1>
                        <p class="np-page-sub">Actualiza los detalles y fotos de tu estancia</p>
                    </div>
                </div>
            </header>

            <div class="np-body">
                <form id="formEditarPropiedad" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="idPropiedad" value="<?php echo $idPropiedad; ?>">

                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-blue"><i class="fa-solid fa-circle-info"></i></span>
                            <div>
                                <h2 class="np-section-title">Información básica</h2>
                                <p class="np-section-desc">Datos principales de la propiedad</p>
                            </div>
                        </div>

                        <div class="np-form-group np-span-full">
                            <label class="np-label">Nombre de la propiedad <span class="np-required">*</span></label>
                            <input id="nombre" type="text" name="nombre" class="np-input" required>
                        </div>

                        <div class="np-grid-2">
                            <div class="np-form-group">
                                <label class="np-label">Tipo de estancia <span class="np-required">*</span></label>
                                <div class="np-select-wrapper">
                                    <select id="idTipoPropiedad" name="idTipoPropiedad" class="np-select" required></select>
                                    <i class="fa-solid fa-chevron-down np-select-arrow"></i>
                                </div>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label">Precio por noche <span class="np-required">*</span></label>
                                <div class="np-input-icon-wrap">
                                    <span class="np-input-prefix">$</span>
                                    <input id="precioNoche" type="number" name="precioNoche" class="np-input np-input-prefixed" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="np-grid-3">
                            <div class="np-form-group">
                                <label class="np-label">Huéspedes</label>
                                <input id="capacidadHuespedes" type="number" name="capacidadHuespedes" class="np-input np-input-center" min="1">
                            </div>
                            <div class="np-form-group">
                                <label class="np-label">Habitaciones</label>
                                <input id="numeroHabitaciones" type="number" name="numeroHabitaciones" class="np-input np-input-center" min="1">
                            </div>
                            <div class="np-form-group">
                                <label class="np-label">Baños</label>
                                <input id="numeroBanos" type="number" name="numeroBanos" class="np-input np-input-center" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-green"><i class="fa-solid fa-location-dot"></i></span>
                            <div>
                                <h2 class="np-section-title">Ubicación</h2>
                            </div>
                        </div>
                        <div class="np-grid-3">
                            <div class="np-form-group">
                                <label class="np-label">País</label>
                                <select id="idPais" class="np-select"></select>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label">Estado</label>
                                <select id="idEstado" class="np-select"></select>
                            </div>
                            <div class="np-form-group">
                                <label class="np-label">Ciudad</label>
                                <select id="idCiudad" name="idCiudad" class="np-select"></select>
                            </div>
                        </div>
                        <div class="np-form-group np-span-full">
                            <label class="np-label">Dirección exacta</label>
                            <textarea id="direccion" name="direccion" class="np-textarea" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-amber"><i class="fa-solid fa-pen-nib"></i></span>
                            <div>
                                <h2 class="np-section-title">Descripción</h2>
                                <p class="np-section-desc">Cuéntale a los huéspedes qué hace especial tu propiedad</p>
                            </div>
                        </div>
                        <div class="np-form-group np-span-full">
                            <label class="np-label">Resumen general</label>
                            <textarea id="descripcion" name="descripcion" class="np-textarea" rows="4"></textarea>
                        </div>
                        <div class="np-form-group np-span-full">
                            <label class="np-label">Especificaciones adicionales</label>
                            <textarea id="especificaciones" name="especificaciones" class="np-textarea" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-purple"><i class="fa-solid fa-images"></i></span>
                            <div>
                                <h2 class="np-section-title">Imágenes</h2>
                                <p class="np-section-desc">Gestiona las fotos actuales o añade nuevas</p>
                            </div>
                        </div>
                        
                        <div id="imagenesActuales" class="current-images-grid"></div>

                        <div class="np-upload-area" id="uploadArea">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: var(--primary);"></i>
                            <p>Haz clic para añadir nuevas fotos</p>
                            <input type="file" name="imagenes[]" id="imagenes" multiple accept=".jpg,.jpeg,.png" style="display:none;">
                        </div>
                        <div class="np-preview-grid" id="previewContainer"></div>
                    </div>

                    <div class="np-section-card">
                        <div class="np-section-header">
                            <span class="np-section-icon np-icon-teal"><i class="fa-solid fa-list-check"></i></span>
                            <h2 class="np-section-title">Servicios, Reglas y Políticas</h2>
                        </div>
                        <div class="np-check-grid" id="contenedorServicios"></div>
                        <hr style="margin: 1.5rem 0; opacity: 0.1;">
                        <div class="np-check-grid" id="contenedorReglas"></div>
                        <hr style="margin: 1.5rem 0; opacity: 0.1;">
                        <div class="np-check-grid" id="contenedorPoliticas"></div>
                    </div>

                    <div class="np-form-actions">
                        <button type="button" class="np-btn-cancel" onclick="window.location.href='propiedades.php'">Cancelar</button>
                        <button type="submit" class="np-btn-save" id="btnGuardar">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../../recursos/js/anfitrion/editar-propiedad.js"></script>
</body>
</html>
