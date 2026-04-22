<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Propiedad | Modo Anfitrión</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/layouts/shared.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/host/main.css">
    <link rel="stylesheet" href="../../recursos/css/host/forms.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="host-body">
    <div class="host-wrapper">
        <aside class="sidebar-host">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="host-logo-box">
                    <h2 style="color: #7c3aed; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-house-laptop"></i>
                        Estancias Digitales
                    </h2>
                    <p>Modo Anfitrión</p>
                </div>
                
                <nav class="side-nav-host">
                    <li class="side-nav-item" onclick="window.location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Inicio</li>
                    <li class="side-nav-item active" onclick="window.location.href='propiedades.php'"><i class="fa-solid fa-building"></i> Propiedades</li>
                    <li class="side-nav-item" onclick="window.location.href='calendario.php'"><i class="fa-solid fa-calendar-days"></i> Calendario</li>
                    <li class="side-nav-item" onclick="window.location.href='reservas.php'"><i class="fa-solid fa-receipt"></i> Reservas</li>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="host-content-main">
            <?php 
            $hide_search = true;
            include '../../recursos/navbar.php'; 
            ?>
            
            <div style="padding: 2.5rem 4rem; max-width: 1200px; margin-left: 0;">
                <header style="margin-bottom: 3.5rem;">
                    <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -1.5px; color: #0f172a; margin-bottom: 0.75rem;">Nueva Propiedad</h1>
                    <p style="color: #64748b; font-size: 16px; font-weight: 500;">Completa los detalles para listar tu estancia en nuestra red exclusiva.</p>
                </header>

                <form action="acciones/guardar-propiedad.php" method="POST">
                    <!-- Información Básica -->
                    <section class="form-section-card">
                        <div class="section-title">
                            <i class="fa-solid fa-circle-info"></i> Información Básica
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 2rem;">
                            <div class="form-group">
                                <label>Nombre de propiedad *</label>
                                <input type="text" placeholder="Ej: Villa Serena con Vista al Mar" required>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Tipo de Estancia *</label>
                                    <select required>
                                        <option value="">Selecciona tipo</option>
                                        <option value="apartamento">Apartamento Moderno</option>
                                        <option value="casa">Casa de Campo</option>
                                        <option value="villa">Villa de Lujo</option>
                                        <option value="loft">Loft Industrial</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ciudad *</label>
                                    <select required>
                                        <option value="">Selecciona ciudad</option>
                                        <option value="barcelona">Barcelona</option>
                                        <option value="madrid">Madrid</option>
                                        <option value="ibiza">Ibiza</option>
                                        <option value="alicante">Alicante</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Dirección Exacta *</label>
                                <textarea rows="3" placeholder="Calle, número, piso..."></textarea>
                                <span class="small-hint">Este campo es obligatorio para la geolocalización.</span>
                            </div>
                        </div>
                    </section>

                    <!-- Detalles y Capacidad -->
                    <section class="form-section-card">
                        <div class="section-title">
                            <i class="fa-solid fa-table-cells-large"></i> Detalles y Capacidad
                        </div>
                        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">
                            <div class="form-group">
                                <label>Precio por noche</label>
                                <div style="position: relative;">
                                    <i class="fa-regular fa-money-bill-1" style="position: absolute; left: 1.25rem; top: 1rem; color: #94a3b8;"></i>
                                    <input type="number" step="0.01" placeholder="0.00" style="padding-left: 3rem;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Huéspedes</label>
                                <input type="number" value="1" min="1">
                            </div>
                            <div class="form-group">
                                <label>Habitaciones</label>
                                <input type="number" value="1" min="1">
                            </div>
                        </div>
                    </section>

                    <!-- Descripción Editorial -->
                    <section class="form-section-card">
                        <div class="section-title">
                            <i class="fa-solid fa-file-lines"></i> Descripción Editorial
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 2rem;">
                            <div class="form-group">
                                <label>Resumen General</label>
                                <textarea rows="5" placeholder="Cuéntales a tus huéspedes por qué tu propiedad es única..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Especificaciones Técnicas</label>
                                <textarea rows="4" placeholder="Detalles sobre electrodomésticos, acceso, etc."></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Servicios y Reglas -->
                    <section class="form-section-card">
                        <div class="form-grid">
                            <div class="form-group">
                                <div class="section-title">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Servicios y Reglas
                                </div>
                                <label style="margin-bottom: 1.5rem; display: block;">SERVICIOS DISPONIBLES</label>
                                <div class="services-grid">
                                    <label class="checkbox-group"><input type="checkbox"> Wifi Alta Velocidad</label>
                                    <label class="checkbox-group"><input type="checkbox"> Piscina</label>
                                    <label class="checkbox-group"><input type="checkbox"> Aire Acondicionado</label>
                                    <label class="checkbox-group"><input type="checkbox"> Estacionamiento</label>
                                    <label class="checkbox-group"><input type="checkbox"> Cocina Equipada</label>
                                    <label class="checkbox-group"><input type="checkbox"> Mascotas Permitidas</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="margin-top: 3.5rem; margin-bottom: 1.5rem; display: block;">POLÍTICAS DE LA CASA</label>
                                <textarea rows="6" placeholder="Ej: No fumar en interiores, silencio después de las 22:00..."></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Galería de Imágenes -->
                    <section class="form-section-card">
                        <div class="section-title">
                            <i class="fa-solid fa-camera-retro"></i> Galería de Imágenes
                        </div>
                        <div class="upload-area">
                            <div class="upload-icon-box">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: #111827; margin-bottom: 0.5rem;">Arrastra tus fotos aquí</h3>
                            <p style="color: #94a3b8; font-size: 14px;">O haz clic para seleccionar archivos desde tu equipo (JPG, PNG hasta 10MB)</p>
                        </div>
                        <div class="img-previews">
                            <div class="img-thumb">
                                <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=300" alt="Preview">
                            </div>
                            <div class="img-thumb"><i class="fa-regular fa-image"></i></div>
                            <div class="img-thumb"><i class="fa-regular fa-image"></i></div>
                            <div class="img-thumb"><i class="fa-regular fa-image"></i></div>
                        </div>
                    </section>

                    <!-- Disponibilidad Inicial -->
                    <section class="form-section-card">
                        <div class="section-title">
                            <i class="fa-solid fa-calendar-check"></i> Disponibilidad Inicial
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Fecha de Inicio</label>
                                <input type="date">
                            </div>
                            <div class="form-group">
                                <label>Fecha de Finalización</label>
                                <input type="date">
                            </div>
                        </div>
                    </section>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='propiedades.php'">Cancelar</button>
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-shield-check"></i> Guardar propiedad
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
