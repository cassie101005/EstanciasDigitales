<?php
session_start();
require_once '../../datos/conexion.php';

// Simular usuario logueado si no hay sesión
$idUsuarioHuesped = isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 2; // ID 2 por defecto para pruebas

// Consultar reservaciones del usuario
$sql = "SELECT r.*, p.vNombre as nombrePropiedad, p.vDescripcion, 
               (SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = p.idPropiedad LIMIT 1) as imagen,
               ci.vNombreCiudad as ciudad, pa.vNombrePais as pais
        FROM tbl_reserva r
        JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad
        LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
        LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
        LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
        WHERE r.idUsuario = ?
        ORDER BY r.dtFechaInicio DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idUsuarioHuesped);
$stmt->execute();
$reservas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservaciones | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: #f8f9fa;">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="reservation-container">
        <header>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">Mis Reservaciones</h1>
            <p style="color: #64748b; font-size: 1.1rem;">Gestiona tus estancias actuales y revisa tus experiencias pasadas.</p>
        </header>

      

        <div class="reservations-list">
            
            <?php if ($reservas->num_rows > 0): ?>
                <?php while ($res = $reservas->fetch_assoc()): ?>
                    <?php 
                        $fechaInicio = new DateTime($res['dtFechaInicio']);
                        $fechaFin = new DateTime($res['dtFechaFin']);
                        $hoy = new DateTime();
                        
                        $status = "CONFIRMADA";
                        $statusColor = "var(--primary)";
                        
                        if ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
                            $status = "EN CURSO";
                            $statusColor = "#008a60";
                        } elseif ($hoy > $fechaFin) {
                            $status = "FINALIZADA";
                            $statusColor = "#6c757d";
                        }
                    ?>
                    <div class="res-card-v2">
                        <div class="res-img-box">
                            <img src="<?php echo htmlspecialchars($res['imagen'] ?? 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=600&q=80'); ?>">
                            <div class="status-badge-v2" style="background: <?php echo $statusColor; ?>;"><?php echo $status; ?></div>
                        </div>
                        <div class="res-content-box">
                            <h2 style="font-size: 1.5rem; font-weight: 700;"><?php echo htmlspecialchars($res['nombrePropiedad']); ?></h2>
                            <div style="font-size: 14px; color: #64748b; display: flex; flex-direction: column; gap: 0.5rem;">
                                <span style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fa-regular fa-calendar"></i> 
                                    <?php echo $fechaInicio->format('d M') . ' - ' . $fechaFin->format('d M, Y'); ?>
                                </span>
                                <span style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fa-solid fa-location-dot"></i> 
                                    <?php echo htmlspecialchars($res['ciudad'] . ', ' . $res['pais']); ?>
                                </span>
                            </div>
                            <div class="res-actions">
                                <button class="btn btn-primary" onclick="window.location.href='detalle_reserva.php?id=<?php echo $res['idPropiedad']; ?>&id_reserva=<?php echo $res['idReserva']; ?>'">Ver detalle</button>
                                <?php if ($status == "FINALIZADA"): ?>
                                    <button class="btn btn-res-grey" onclick="openCommentModal(<?php echo $res['idReserva']; ?>, <?php echo $res['idPropiedad']; ?>, '<?php echo addslashes($res['nombrePropiedad']); ?>')">
                                        <i class="fa-regular fa-comment-dots"></i> Añadir comentarios
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="res-price-abs">$<?php echo number_format($res['dTotalReserva'], 0); ?> <span>total</span></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem; background: white; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1.5rem;"></i>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #64748b;">No tienes reservaciones aún</h2>
                    <p style="color: #94a3b8; margin-top: 0.5rem;">¡Explora nuestras propiedades y planea tu próximo viaje!</p>
                    <button class="btn btn-primary" onclick="window.location.href='home.php'" style="margin-top: 2rem;">Explorar Marketplace</button>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <footer class="main-footer" style="padding: 4rem 10%; margin-top: 6rem; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div>Estancias Digitales © 2024. Tus viajes, simplificados.</div>
            <div class="footer-links">
                <a href="#">Privacidad</a>
                <a href="#">Ayuda</a>
            </div>
        </div>
    </footer>
    <!-- Modal de Comentarios -->
    <div id="commentModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;">
        <div class="modal-content" style="background:white; padding:2.5rem; border-radius:2rem; width:100%; max-width:500px; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
            <h2 id="modalPropTitle" style="font-size:1.5rem; font-weight:800; margin-bottom:1rem;">Añadir Comentario</h2>
            <p style="color:#64748b; margin-bottom:2rem;">Cuéntanos cómo fue tu experiencia en esta estancia.</p>
            
            <form id="commentForm">
                <input type="hidden" name="idReserva" id="modalIdReserva">
                <input type="hidden" name="idPropiedad" id="modalIdPropiedad">
                
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-weight:700; margin-bottom:0.5rem;">Calificación</label>
                    <div class="star-rating" style="display:flex; gap:0.5rem; font-size:1.5rem; color:#fbbf24; cursor:pointer;">
                        <i class="fa-solid fa-star" onclick="setRating(1)"></i>
                        <i class="fa-solid fa-star" onclick="setRating(2)"></i>
                        <i class="fa-solid fa-star" onclick="setRating(3)"></i>
                        <i class="fa-solid fa-star" onclick="setRating(4)"></i>
                        <i class="fa-solid fa-star" onclick="setRating(5)"></i>
                    </div>
                    <input type="hidden" name="calificacion" id="modalRating" value="5">
                </div>

                <div style="margin-bottom:2rem;">
                    <label style="display:block; font-weight:700; margin-bottom:0.5rem;">Tu comentario</label>
                    <textarea name="comentario" required style="width:100%; height:120px; padding:1rem; border-radius:1rem; border:1px solid #e2e8f0; font-family:inherit; resize:none;" placeholder="Describe tu estancia..."></textarea>
                </div>

                <div style="display:flex; gap:1rem;">
                    <button type="button" class="btn btn-primary" style="flex:1; justify-content:center;" onclick="saveComment()">Publicar comentario</button>
                    <button type="button" class="btn btn-res-grey" style="flex:1; justify-content:center;" onclick="closeCommentModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openCommentModal(idRes, idProp, title) {
            document.getElementById('modalIdReserva').value = idRes;
            document.getElementById('modalIdPropiedad').value = idProp;
            document.getElementById('modalPropTitle').innerText = 'Opinión sobre ' + title;
            document.getElementById('commentModal').style.display = 'flex';
            setRating(5);
        }

        function closeCommentModal() {
            document.getElementById('commentModal').style.display = 'none';
            document.getElementById('commentForm').reset();
        }

        function setRating(n) {
            document.getElementById('modalRating').value = n;
            const stars = document.querySelectorAll('.star-rating i');
            stars.forEach((s, idx) => {
                if (idx < n) {
                    s.classList.replace('fa-regular', 'fa-solid');
                } else {
                    s.classList.replace('fa-solid', 'fa-regular');
                }
            });
        }

        async function saveComment() {
            const formData = new FormData(document.getElementById('commentForm'));
            try {
                const response = await fetch('../../apis/huesped/guardar_comentario.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.ok) {
                    Swal.fire('¡Gracias!', data.mensaje, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.mensaje, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        }
    </script>
</body>
</html>
