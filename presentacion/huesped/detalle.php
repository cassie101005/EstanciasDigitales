<?php
session_start();
require_once '../../datos/conexion.php';

// Obtener ID de la propiedad
$idPropiedad = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idPropiedad <= 0) {
    header("Location: home.php");
    exit();
}

// Consultar detalles de la propiedad
$sql = "SELECT p.*, u.vNombre as hostNombre, u.vApellido as hostApellido, 
               tp.vNombreCategoria as tipo, ci.vNombreCiudad as ciudad, 
               es.vNombreEstado as estado, pa.vNombrePais as pais
        FROM tbl_propiedad p
        JOIN tbl_usuarios u ON p.idUsuario = u.idUsuario
        LEFT JOIN tbl_tipo_propiedad tp ON p.idTipoPropiedad = tp.idTipoPropiedad
        LEFT JOIN tbl_ciudad ci ON p.idCiudad = ci.idCiudad
        LEFT JOIN tbl_estado es ON ci.idEstado = es.idEstado
        LEFT JOIN tbl_pais pa ON es.idPais = pa.idPais
        WHERE p.idPropiedad = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idPropiedad);
$stmt->execute();
$propResult = $stmt->get_result();
$prop = $propResult->fetch_assoc();

if (!$prop) {
    header("Location: home.php");
    exit();
}

// Consultar imágenes
$sqlImages = "SELECT vImagen FROM tbl_imagen_propiedad WHERE idPropiedad = ? ORDER BY idImagen ASC";
$stmtImages = $conexion->prepare($sqlImages);
$stmtImages->bind_param("i", $idPropiedad);
$stmtImages->execute();
$imagesResult = $stmtImages->get_result();
$images = [];
while ($row = $imagesResult->fetch_assoc()) {
    $images[] = "../../" . str_replace(' ', '%20', $row['vImagen']);
}
$mainImage = !empty($images) ? $images[0] : "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80";
$secondaryImages = array_slice($images, 1, 2);

// Consultar servicios
$sqlServices = "SELECT se.vNombreServicio 
                FROM tbl_propiedad_servicios ps
                JOIN tbl_servicios_extra se ON ps.idServicio = se.idServicio
                WHERE ps.idPropiedad = ?";
$stmtServices = $conexion->prepare($sqlServices);
$stmtServices->bind_param("i", $idPropiedad);
$stmtServices->execute();
$servicesResult = $stmtServices->get_result();
$services = [];
while ($row = $servicesResult->fetch_assoc()) {
    $services[] = $row['vNombreServicio'];
}

// Consultar reglas
$sqlRules = "SELECT r.vNombreRegla 
             FROM tbl_propiedad_regla pr
             JOIN tbl_reglas r ON pr.idRegla = r.idRegla
             WHERE pr.idPropiedad = ?";
$stmtRules = $conexion->prepare($sqlRules);
$stmtRules->bind_param("i", $idPropiedad);
$stmtRules->execute();
$rulesResult = $stmtRules->get_result();
$rules = [];
while ($row = $rulesResult->fetch_assoc()) {
    $rules[] = $row['vNombreRegla'];
}

// Consultar políticas
$sqlPolicies = "SELECT pol.vNombrePol 
                FROM tbl_propiedad_politica pp
                JOIN tbl_politicas pol ON pp.idPolitica = pol.idPolitica
                WHERE pp.idPropiedad = ?";
$stmtPolicies = $conexion->prepare($sqlPolicies);
$stmtPolicies->bind_param("i", $idPropiedad);
$stmtPolicies->execute();
$policiesResult = $stmtPolicies->get_result();
$policies = [];
while ($row = $policiesResult->fetch_assoc()) {
    $policies[] = $row['vNombrePol'];
}

// NUEVO: Consultar reseñas (máximo 5)
$sqlResenias = "SELECT r.*, u.vNombre, u.vApellido, u.vFoto
                FROM tbl_resenia r
                JOIN tbl_usuarios u ON r.idUsuario = u.idUsuario
                WHERE r.idPropiedad = ?
                ORDER BY r.dtFechaResenia DESC
                LIMIT 5";
$stmtRes = $conexion->prepare($sqlResenias);
$stmtRes->bind_param("i", $idPropiedad);
$stmtRes->execute();
$resenias = $stmtRes->get_result();

// Verificar si el usuario actual ya calificó
$yaCalifico = false;
$miCalificacion = 0;
if (isset($_SESSION['idUsuario'])) {
    $sqlCheck = "SELECT iCalificacion FROM tbl_resenia WHERE idPropiedad = ? AND idUsuario = ? AND iCalificacion > 0";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $idPropiedad, $_SESSION['idUsuario']);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($rowCheck = $resCheck->fetch_assoc()) {
        $yaCalifico = true;
        $miCalificacion = $rowCheck['iCalificacion'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($prop['vNombre']); ?> | Estancias Digitales</title>
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .input-hidden { position: absolute; opacity: 0; pointer-events: none; }
        .date-picker-trigger { cursor: pointer; transition: background 0.2s; border-radius: 8px; }
        .date-picker-trigger:hover { background: #f0f0f0; }
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            cursor: pointer;
            opacity: 0;
        }
    </style>
</head>
<body style="background: var(--surface);">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="detail-container" style="max-width: 1600px; width: 95%; padding: 4rem 0;">
        <header class="detail-header">
            <h1 class="detail-title"><?php echo htmlspecialchars($prop['vNombre']); ?></h1>
            <p class="detail-subtitle"><?php echo htmlspecialchars($prop['ciudad'] . ', ' . $prop['estado'] . ', ' . $prop['pais']); ?> • <span style="color: var(--primary);"><?php echo htmlspecialchars($prop['tipo']); ?></span></p>
        </header>

        <section class="gallery-section" style="height: 700px;">
            <div class="main-img"><img src="<?php echo htmlspecialchars($mainImage); ?>" style="width:100%; height:100%; object-fit:cover;"></div>
            <div class="gallery-grid" style="display: grid; grid-template-rows: 1fr 1fr; gap: 0.5rem;">
                <?php foreach ($secondaryImages as $img): ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" style="width:100%; height:100%; object-fit:cover;">
                <?php endforeach; ?>
                <?php if (count($secondaryImages) < 2): ?>
                    <img src="https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=600&q=80" style="width:100%; height:100%; object-fit:cover;">
                    <?php if (count($secondaryImages) < 1): ?>
                        <img src="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=600&q=80" style="width:100%; height:100%; object-fit:cover;">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <div class="detail-main-grid" style="grid-template-columns: 1fr 450px; gap: 8rem;">
            <main>
                <div class="host-badge" style="border-bottom: 1px solid var(--surface-container-high); padding-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">Anfitrión: <?php echo htmlspecialchars($prop['hostNombre'] . ' ' . $prop['hostApellido']); ?></h2>
                        <p style="color: var(--on-surface-variant); font-weight: 600;"><?php echo $prop['iCapacidadHuespedes']; ?> huéspedes · <?php echo $prop['iNumeroHabitaciones']; ?> habitaciones</p>
                    </div>
                    <div style="width: 64px; height: 64px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img src="https://i.pravatar.cc/100?u=<?php echo $prop['idUsuario']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <article style="padding: 2rem 0; border-top: 1px solid #eee;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2rem;">Descripción</h2>
                    <div style="font-size: 1.15rem; line-height: 1.8; color: var(--on-surface-variant); max-width: 800px;">
                        <p style="margin-bottom: 1.5rem;"><?php echo nl2br(htmlspecialchars($prop['vDescripcion'])); ?></p>
                    </div>
                </article>

                <section style="padding: 4rem 0; border-top: 1px solid #eee;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2.5rem;">Servicios e Instalaciones</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; font-weight: 600;">
                        <?php foreach (array_slice($services, 0, 6) as $service): ?>
                            <div style="display:flex; align-items:center; gap: 1.25rem;">
                                <i class="fa-solid fa-check" style="width: 24px; color: var(--primary);"></i> 
                                <?php echo htmlspecialchars($service); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section style="padding: 4rem 0; border-top: 1px solid #eee;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; text-transform: uppercase; margin-bottom: 2.5rem;">Opiniones de los huéspedes</h2>
                    
                    <?php if (isset($_SESSION['idUsuario'])): ?>
                    <!-- Formulario de Reseña -->
                    <div id="formReseniaBox" style="background: white; border: 1px solid #eee; padding: 2rem; border-radius: 1.5rem; margin-bottom: 3rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem;">Deja tu opinión</h3>
                        <form id="formResenia">
                            <input type="hidden" name="idPropiedad" value="<?php echo $idPropiedad; ?>">
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: #64748b;">Calificación (Opcional)</label>
                                <div class="star-rating" style="display: flex; gap: 0.5rem; font-size: 1.5rem; color: #cbd5e1; <?php echo $yaCalifico ? 'pointer-events: none; opacity: 0.8;' : ''; ?>">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="fa-solid fa-star star-btn" data-value="<?php echo $i; ?>" 
                                           style="cursor: pointer; <?php echo ($yaCalifico && $i <= $miCalificacion) ? 'color: #fbbf24;' : ''; ?>"></i>
                                    <?php endfor; ?>
                                    <input type="hidden" name="iCalificacion" id="inputCalificacion" value="<?php echo $miCalificacion; ?>">
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: #64748b;">Comentario</label>
                                <textarea name="vComentario" style="width: 100%; border: 1px solid #eee; border-radius: 1rem; padding: 1rem; font-family: inherit; resize: vertical; min-height: 100px; outline: none; transition: border-color 0.2s;" placeholder="Cuéntanos tu experiencia..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 800;">Enviar Reseña</button>
                        </form>
                    </div>
                    <?php else: ?>
                    <div style="padding: 1.5rem; background: #f8fafc; border-radius: 1rem; margin-bottom: 3rem; text-align: center; color: #64748b;">
                        Para dejar una reseña, por favor <a href="../../negocio/auth/login.php" style="color: var(--primary); font-weight: 700;">inicia sesión</a>.
                    </div>
                    <?php endif; ?>

                    <?php if ($resenias->num_rows > 0): ?>
                        <div style="display: grid; gap: 2rem;">
                            <?php while ($res = $resenias->fetch_assoc()): ?>
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="<?php echo !empty($res['vFoto']) ? '../../' . $res['vFoto'] : 'https://i.pravatar.cc/100?u=' . $res['idUsuario']; ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                            <div>
                                                <p style="font-weight: 800;"><?php echo htmlspecialchars($res['vNombre'] . ' ' . $res['vApellido']); ?></p>
                                                <p style="font-size: 12px; color: #64748b;"><?php echo date('d M, Y', strtotime($res['dtFechaResenia'])); ?></p>
                                            </div>
                                        </div>
                                        <div style="color: #fbbf24;">
                                            <?php for($i=0; $i<$res['iCalificacion']; $i++): ?>
                                                <i class="fa-solid fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p style="color: #475569; line-height: 1.6; font-style: italic;">"<?php echo htmlspecialchars($res['vComentario']); ?>"</p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; background: #f1f5f9; border-radius: 1.5rem; color: #64748b;">
                            <i class="fa-regular fa-comment-dots" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            Aún no hay opiniones para esta propiedad. ¡Sé el primero en hospedarte!
                        </div>
                    <?php endif; ?>
                </section>
            </main>

            <aside>
                <form action="pago.php" method="POST" id="reservationForm">
                    <input type="hidden" name="idPropiedad" value="<?php echo $idPropiedad; ?>">
                    <input type="hidden" name="precioNoche" id="precioNocheInput" value="<?php echo $prop['dPrecioNoche']; ?>">
                    <input type="hidden" name="total" id="totalInput" value="0">
                    <input type="hidden" name="noches" id="nochesInput" value="0">

                    <div class="tonal-card reservation-sidebar" style="width: 100%; position: sticky; top: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2rem;">
                            <span style="font-size: 1.75rem; font-weight: 800;">$<?php echo number_format($prop['dPrecioNoche'], 0); ?> <span style="font-size: 0.9rem; font-weight: 400; color: #6d7083;">MXN / noche</span></span>
                            <span style="font-weight: 700; font-size: 14px;"><i class="fa-solid fa-star" style="color: var(--primary);"></i> 4.98</span>
                        </div>

                        <div style="border: 1px solid #ddd; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="display: flex; border-bottom: 1px solid #ddd;">
                                <div style="flex:1; padding: 0.75rem; position: relative;" class="date-picker-trigger">
                                    <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Llegada</label>
                                    <input type="date" name="fechaInicio" id="fechaInicio" required style="width: 100%; border: none; background: transparent; font-size: 14px; outline: none;" onchange="updateReservationSummary()">
                                </div>
                                <div style="flex:1; padding: 0.75rem; border-left: 1px solid #ddd; position: relative;" class="date-picker-trigger">
                                    <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Salida</label>
                                    <input type="date" name="fechaFin" id="fechaFin" required style="width: 100%; border: none; background: transparent; font-size: 14px; outline: none;" onchange="updateReservationSummary()">
                                </div>
                            </div>
                            <div style="padding: 0.75rem;">
                                <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Huéspedes</label>
                                <select name="huespedes" id="huespedes" style="width: 100%; border: none; background: transparent; font-size: 14px; padding: 0; outline: none;">
                                    <?php for($i=1; $i<=$prop['iCapacidadHuespedes']; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> huésped<?php echo $i>1?'es':''; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-weight: 800;">Reservar Estancia</button>
                        
                        <div id="summaryList" style="display: none; margin-top: 2rem;">
                            <ul style="border-top: 1px solid #eee; padding-top: 2rem; list-style: none;">
                                <li style="display:flex; justify-content: space-between; margin-bottom: 1rem;">
                                    <span style="text-decoration: underline;">$<?php echo number_format($prop['dPrecioNoche'], 0); ?> MXN x <span id="summaryNights">0</span> noches</span> 
                                    <span id="summaryBasePrice">$0 MXN</span>
                                </li>
                                <li style="display:flex; justify-content: space-between; margin-bottom: 1rem;">
                                    <span style="text-decoration: underline;">Tarifa de limpieza</span> <span>$1,200 MXN</span>
                                </li>
                                <li style="display:flex; justify-content: space-between; padding-top: 1.5rem; border-top: 1px solid #eee; font-weight: 800; font-size: 1.25rem;">
                                    <span>Total</span> <span id="summaryTotal">$0 MXN</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </aside>
        </div>
    </div>

    <script>
        const precioNoche = <?php echo $prop['dPrecioNoche']; ?>;
        const tarifaLimpieza = 1200;

        function updateReservationSummary() {
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;
            const summaryList = document.getElementById('summaryList');

            if (fechaInicio && fechaFin) {
                const start = new Date(fechaInicio);
                const end = new Date(fechaFin);
                
                if (end > start) {
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    const basePrice = diffDays * precioNoche;
                    const total = basePrice + tarifaLimpieza;
                    
                    document.getElementById('summaryNights').innerText = diffDays;
                    document.getElementById('summaryBasePrice').innerText = '$' + basePrice.toLocaleString() + ' MXN';
                    document.getElementById('summaryTotal').innerText = '$' + total.toLocaleString() + ' MXN';
                    
                    document.getElementById('nochesInput').value = diffDays;
                    document.getElementById('totalInput').value = total;
                    
                    summaryList.style.display = 'block';
                } else {
                    summaryList.style.display = 'none';
                }
            }
        }

        // Establecer fecha mínima como mañana
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('fechaInicio').min = tomorrow.toISOString().split('T')[0];
        
        document.getElementById('fechaInicio').addEventListener('change', () => {
            const nextDay = new Date(document.getElementById('fechaInicio').value);
            nextDay.setDate(nextDay.getDate() + 1);
            document.getElementById('fechaFin').min = nextDay.toISOString().split('T')[0];
        });
        // Lógica de Reseñas
        document.querySelectorAll('.star-btn').forEach(star => {
            star.onclick = function() {
                const val = this.getAttribute('data-value');
                document.getElementById('inputCalificacion').value = val;
                
                // Actualizar visual de estrellas
                document.querySelectorAll('.star-btn').forEach(s => {
                    if (s.getAttribute('data-value') <= val) {
                        s.style.color = '#fbbf24';
                    } else {
                        s.style.color = '#cbd5e1';
                    }
                });
            }
        });

        const formResenia = document.getElementById('formResenia');
        if (formResenia) {
            formResenia.onsubmit = async (e) => {
                e.preventDefault();
                
                const fd = new FormData(formResenia);
                const btn = formResenia.querySelector('button');
                btn.disabled = true;
                btn.innerText = 'Enviando...';

                try {
                    const res = await fetch('../../apis/huesped/resenia.php', {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();

                    if (data.ok) {
                        alert(data.mensaje);
                        location.reload(); // Recargar para ver el comentario
                    } else {
                        alert(data.error);
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error de conexión al enviar la reseña.');
                } finally {
                    btn.disabled = false;
                    btn.innerText = 'Enviar Reseña';
                }
            };
        }
    </script>
</body>
</html>
