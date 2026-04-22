<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Propiedad - Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/anfitrion/dashboard.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/propiedades.css">
    <link rel="stylesheet" href="../../recursos/css/anfitrion/nueva-propiedad.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon">
                    <i class="fa-solid fa-house"></i>
                </div>
                <div>
                    <h3>Estancias</h3>
                    <p>Panel de anfitrión</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="propiedades.php" class="nav-item active">
                    <i class="fa-solid fa-building"></i>
                    <span>Mis Propiedades</span>
                </a>
                <a href="reservas.php" class="nav-item">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Reservas</span>
                </a>
                <a href="calendario.php" class="nav-item">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Calendario</span>
                </a>
                <a href="perfil.php" class="nav-item">
                    <i class="fa-solid fa-user"></i>
                    <span>Perfil</span>
                </a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h1>Registrar nueva propiedad</h1>
                    <p>Completa la información de tu estancia</p>
                </div>
            </header>

            <section class="content-section">
                <div class="form-card">

                    <form id="formNuevaPropiedad" method="POST" enctype="multipart/form-data">
                        
                        <input type="hidden" name="idUsuario" value="2">

                        <div class="form-group">
                            <label>Nombre de propiedad *</label>
                            <input type="text" name="nombre" placeholder="Ej: Villa Serena con Vista al Mar" required>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tipo de Estancia *</label>
                                <select name="idTipoPropiedad" id="idTipoPropiedad" required>
                                    <option value="">Selecciona tipo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Ciudad *</label>
                                <select name="idCiudad" id="idCiudad" required>
                                    <option value="">Selecciona ciudad</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección Exacta *</label>
                            <textarea name="direccion" rows="3" placeholder="Calle, número, piso..." required></textarea>
                            <span class="small-hint">Este campo es obligatorio para la ubicación de la propiedad.</span>
                        </div>

                        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">
                            <div class="form-group">
                                <label>Precio por noche *</label>
                                <div style="position: relative;">
                                    <i class="fa-regular fa-money-bill-1" style="position: absolute; left: 1.25rem; top: 1rem; color: #94a3b8;"></i>
                                    <input type="number" name="precioNoche" step="0.01" placeholder="0.00" style="padding-left: 3rem;" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Huéspedes *</label>
                                <input type="number" name="capacidadHuespedes" value="1" min="1" required>
                            </div>

                            <div class="form-group">
                                <label>Habitaciones *</label>
                                <input type="number" name="numeroHabitaciones" value="1" min="1" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Resumen General</label>
                            <textarea name="descripcion" rows="5" placeholder="Cuéntales a tus huéspedes por qué tu propiedad es única..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Especificaciones</label>
                            <textarea name="especificaciones" rows="4" placeholder="Detalles adicionales de la propiedad..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Imágenes de la propiedad</label>
                            <input type="file" name="imagenes[]" id="imagenes" multiple accept=".jpg,.jpeg,.png">
                        </div>

                        <div class="form-group">
                            <label>SERVICIOS DISPONIBLES</label>
                            <div class="services-grid" id="contenedorServicios"></div>
                        </div>

                        <div class="form-group">
                            <label>REGLAS</label>
                            <div class="services-grid" id="contenedorReglas"></div>
                        </div>

                        <div class="form-group">
                            <label>POLÍTICAS</label>
                            <div class="services-grid" id="contenedorPoliticas"></div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="window.location.href='propiedades.php'">
                                Cancelar
                            </button>

                            <button type="submit" class="btn-save">
                                <i class="fa-solid fa-shield-check"></i> Guardar propiedad
                            </button>
                        </div>
                    </form>

                </div>
            </section>
        </main>
    </div>

    <script src="../../recursos/js/anfitrion/nueva-propiedad.js"></script>
</body>
</html>