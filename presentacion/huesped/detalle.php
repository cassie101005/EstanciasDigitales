<?php
require_once '../../negocio/auth/verificar_sesion.php';
validarSesion('huesped', '../../');
require_once '../../datos/conexion.php';
require_once '../../negocio/utilidades/seguridad.php';

// Obtener ID de la propiedad
$idPropiedad = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idPropiedad <= 0) {
    header("Location: home.php");
    exit();
}

require_once '../../negocio/huesped/detalle_view.php';

// 1. Consultar detalles de la propiedad
$prop = getPropertyDetail($idPropiedad, $conexion);

if (!$prop) {
    header("Location: home.php");
    exit();
}

// 2. Consultar imágenes
$images = getPropertyImages($idPropiedad, $conexion);
$mainImage = !empty($images) ? $images[0] : "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80";
$secondaryImages = array_slice($images, 1, 2);

// 3. Consultar servicios, reglas y políticas
$services = getPropertyServices($idPropiedad, $conexion);
$reglas = getPropertyReglas($idPropiedad, $conexion);
$politicas = getPropertyPoliticas($idPropiedad, $conexion);

// 4. Consultar reseñas y comentarios
$resenias = getPropertyResenias($idPropiedad, $conexion);

// 5. Consultar fechas ya reservadas y bloqueos
$reservedDates = getReservedDates($idPropiedad, $conexion);

// 6. Consultar tarifas especiales
$specialRates = getSpecialRates($idPropiedad, $conexion);

// 7. Verificar si el usuario actual ya calificó y límite de comentarios
$yaCalifico = false;
$miCalificacion = 0;
$totalReseniasHuesped = 0;
if (isset($_SESSION['idUsuario'])) {
    require_once '../../negocio/huesped/resenia.php';
    $resNeg = new ReseniaNegocio($conexion);
    $totalReseniasHuesped = $resNeg->getReviewsCount($_SESSION['idUsuario'], $idPropiedad);
    list($yaCalifico, $miCalificacion) = checkUserCalifico($idPropiedad, $_SESSION['idUsuario'], $conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($prop['vNombre']); ?> | Estancias Digitales</title>
    <link rel="icon" type="image/png" href="../../recursos/img/logo.png">
    <link rel="stylesheet" href="../../recursos/css/variables.css">
    <link rel="stylesheet" href="../../recursos/css/main.css">
    <link rel="stylesheet" href="../../recursos/css/components/navbar.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../recursos/css/huesped/detalle.css">
</head>
<body style="background: var(--surface);">
    <?php include '../../recursos/navbar.php'; ?>

    <div class="property-detail-page">
        <header class="detail-header">
            <h1 class="property-title"><?php echo htmlspecialchars($prop['vNombre']); ?></h1>
            <?php if (!empty($prop['direccion'])): ?>
            <p class="property-location" style="margin-bottom: 0.25rem;">
                <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 4px;"></i>
                <?php echo htmlspecialchars($prop['direccion']); ?>
            </p>
            <?php endif; ?>
            <p class="property-location"><?php echo htmlspecialchars($prop['ciudad'] . ', ' . $prop['estado'] . ', ' . $prop['pais']); ?> • <span style="color: var(--primary);"><?php echo htmlspecialchars($prop['tipo']); ?></span></p>
        </header>

        <div class="property-gallery">
            <div class="gallery-item main-item" onclick="openGallery(0)">
                <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="Imagen principal">
            </div>
            <div class="gallery-side">
                <?php 
                $img1 = isset($images[1]) ? $images[1] : "https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=600&q=80";
                $img2 = isset($images[2]) ? $images[2] : "https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=600&q=80";
                $totalImgs = count($images);
                ?>
                <div class="gallery-item" onclick="openGallery(1)">
                    <img src="<?php echo htmlspecialchars($img1); ?>" alt="Imagen secundaria 1">
                </div>
                <div class="gallery-item" onclick="openGallery(2)" style="position: relative;">
                    <img src="<?php echo htmlspecialchars($img2); ?>" alt="Imagen secundaria 2">
                    <?php if ($totalImgs > 3): ?>
                        <div class="gallery-more-overlay">
                            +<?php echo ($totalImgs - 3); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="detail-layout">
            <main class="main-detail-content">
                
                <div class="detail-section">
                    <div class="host-badge" style="border-bottom: 1px solid var(--surface-container-high); padding-bottom: 1.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 class="detail-section-title" style="margin-bottom: 0.5rem;">Anfitrión: <?php echo htmlspecialchars($prop['hostNombre'] . ' ' . $prop['hostApellido']); ?></h2>
                            <p style="color: var(--on-surface-variant); font-weight: 600;"><?php echo $prop['iCapacidadHuespedes']; ?> huéspedes · <?php echo $prop['iNumeroHabitaciones']; ?> habitaciones</p>
                            <?php if (!empty($prop['direccion'])): ?>
                            <p style="color: var(--on-surface-variant); font-size: 13px; margin-top: 0.4rem;">
                                <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 4px;"></i>
                                <?php echo htmlspecialchars($prop['direccion'] . ', ' . $prop['ciudad'] . ', ' . $prop['estado']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div style="width: 56px; height: 56px; border-radius: 50%; overflow: hidden; flex-shrink: 0; background: var(--primary);">
                            <?php if (!empty($prop['hostFoto'])): ?>
                                <img src="../../<?php echo str_replace(' ', '%20', htmlspecialchars($prop['hostFoto'])); ?>" 
                                     alt="<?php echo htmlspecialchars($prop['hostNombre']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; font-weight: 800;">
                                    <?php echo strtoupper(mb_substr($prop['hostNombre'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h2 class="detail-section-title">Descripción</h2>
                    <div style="font-size: 1.1rem; line-height: 1.7; color: #4b5563;">
                        <p><?php echo nl2br(htmlspecialchars($prop['vDescripcion'])); ?></p>
                    </div>
                </div>

                <div class="detail-section">
                    <h2 class="detail-section-title">Servicios e Instalaciones</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; color: #374151; font-weight: 500;">
                        <?php foreach ($services as $service): ?>
                            <div style="display:flex; align-items:center; gap: 0.75rem;">
                                <i class="fa-solid fa-check" style="color: var(--primary); font-size: 0.9rem;"></i> 
                                <?php echo htmlspecialchars($service); ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($services)): ?>
                            <p style="color: #94a3b8; font-style: italic;">No se especificaron servicios.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-section">
                    <h2 class="detail-section-title">Reglas de la estancia</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; color: #374151; font-weight: 500;">
                        <?php foreach ($reglas as $regla): ?>
                            <div style="display:flex; align-items:center; gap: 0.75rem;">
                                <i class="fa-solid fa-check" style="color: var(--primary); font-size: 0.9rem;"></i> 
                                <?php echo htmlspecialchars($regla); ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($reglas)): ?>
                            <p style="color: #94a3b8; font-style: italic;">No hay reglas específicas.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-section">
                    <h2 class="detail-section-title">Políticas de la propiedad</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; color: #374151; font-weight: 500;">
                        <?php foreach ($politicas as $politica): ?>
                            <div style="display:flex; align-items:center; gap: 0.75rem;">
                                <i class="fa-solid fa-check" style="color: var(--primary); font-size: 0.9rem;"></i> 
                                <?php echo htmlspecialchars($politica); ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($politicas)): ?>
                            <p style="color: #94a3b8; font-style: italic;">No se especificaron políticas.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-section">
                    <h2 class="detail-section-title">Opiniones de los huéspedes</h2>
                    
                    <?php if (isset($_SESSION['idUsuario'])): ?>
                    <!-- Formulario de Reseña -->
                    <div id="formReseniaBox" style="position: relative; z-index: 5; background: white; border: 1px solid #eee; padding: 2rem; border-radius: 1.5rem; margin-bottom: 3rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03); max-width: 800px;">
                        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem;">Deja tu opinión</h3>
                        <form id="formResenia" style="position: relative; z-index: 10;">
                            <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                            <input type="hidden" name="idPropiedad" value="<?php echo $idPropiedad; ?>">
                            <input type="hidden" name="idResenia" id="inputIdResenia" value="0">
                            
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
                                <textarea name="vComentario" style="width: 100%; border: 1px solid #eee; border-radius: 1rem; padding: 1.25rem; font-family: inherit; resize: vertical; min-height: 120px; outline: none; transition: border-color 0.2s; position: relative; z-index: 11; cursor: text; background: #fcfcfc;" placeholder="Cuéntanos tu experiencia..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2.5rem; font-weight: 800;">Enviar Reseña</button>
                        </form>
                    </div>
                    <?php else: ?>
                    <div style="padding: 1.5rem; background: #f8fafc; border-radius: 1rem; margin-bottom: 3rem; text-align: center; color: #64748b;">
                        Para dejar una reseña, por favor <a href="../../negocio/auth/login.php" style="color: var(--primary); font-weight: 700;">inicia sesión</a>.
                    </div>
                    <?php endif; ?>

                    <?php if (count($resenias) > 0): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                            <?php foreach ($resenias as $res): ?>
                                <div style="background: #f8fafc; padding: 2rem; border-radius: 1.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <img src="<?php echo !empty($res['vFoto']) ? '../../' . $res['vFoto'] : 'https://i.pravatar.cc/100?u=' . $res['idUsuario']; ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                                            <div>
                                                <p style="font-weight: 800;"><?php echo htmlspecialchars($res['vNombre'] . ' ' . $res['vApellido']); ?></p>
                                                <p style="font-size: 12px; color: #64748b;"><?php echo date('d M, Y', strtotime($res['fecha'])); ?></p>
                                                <?php if (isset($_SESSION['idUsuario']) && $_SESSION['idUsuario'] == $res['idUsuario']): ?>
                                                    <button type="button" class="btn-edit-comment" 
                                                            onclick="editComment(<?php echo $res['id']; ?>, '<?php echo addslashes($res['vComentario']); ?>')"
                                                            style="background: none; border: none; color: var(--primary); font-size: 11px; font-weight: 700; cursor: pointer; padding: 0; margin-top: 4px; text-transform: uppercase;">
                                                        <i class="fa-solid fa-pen-to-square"></i> Editar
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="color: #fbbf24;">
                                            <?php for($i=0; $i<$res['iCalificacion']; $i++): ?>
                                                <i class="fa-solid fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p style="color: #475569; line-height: 1.6; font-style: italic;">"<?php echo htmlspecialchars($res['vComentario']); ?>"</p>

                                    <?php if (!empty($res['vRespuesta'])): ?>
                                        <div style="margin-top: 1.25rem; background: white; border-left: 3px solid var(--primary); border-radius: 0 12px 12px 0; padding: 1rem 1.25rem; display: flex; gap: 1rem; align-items: flex-start;">
                                            <div style="flex-shrink: 0; width: 36px; height: 36px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <?php 
                                                    $hostImg = !empty($res['hostFoto']) ? '../../' . $res['hostFoto'] : '';
                                                ?>
                                                <?php if ($hostImg): ?>
                                                    <img src="<?php echo htmlspecialchars($hostImg); ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fa-solid fa-house-laptop" style="color: white; font-size: 14px;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p style="font-size: 12px; font-weight: 800; color: var(--primary); margin: 0 0 4px 0;">
                                                    <i class="fa-solid fa-reply" style="margin-right: 4px;"></i>
                                                    Respuesta del anfitrión
                                                    <?php if (!empty($res['dtFechaRespuesta'])): ?>
                                                        <span style="font-weight: 400; color: #94a3b8; margin-left: 8px;"><?php echo date('d M, Y', strtotime($res['dtFechaRespuesta'])); ?></span>
                                                    <?php endif; ?>
                                                </p>
                                                <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0;"><?php echo htmlspecialchars($res['vRespuesta']); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; background: #f1f5f9; border-radius: 1.5rem; color: #64748b;">
                            <i class="fa-regular fa-comment-dots" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            Aún no hay opiniones para esta propiedad. ¡Sé el primero en hospedarte!
                        </div>
                    <?php endif; ?>
                </div>
            </main>

            <aside>
                <form action="pago.php" method="POST" id="reservationForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
                    <input type="hidden" name="idPropiedad" value="<?php echo $idPropiedad; ?>">
                    <input type="hidden" name="precioNoche" id="precioNocheInput" value="<?php echo $prop['dPrecioNoche']; ?>">
                    <input type="hidden" name="total" id="totalInput" value="0">
                    <input type="hidden" name="noches" id="nochesInput" value="0">

                    <div class="tonal-card reservation-sidebar" style="width: 100%; position: sticky; top: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2rem;">
                            <span style="font-size: 1.75rem; font-weight: 800;">$<?php echo number_format($prop['dPrecioNoche'], 0); ?> <span style="font-size: 0.9rem; font-weight: 400; color: #6d7083;">MXN / noche</span></span>

                        </div>

                        <div style="border: 1px solid #ddd; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="display: flex; border-bottom: 1px solid #ddd;">
                                <div style="flex:1; padding: 0.75rem; position: relative;" class="date-picker-trigger">
                                    <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Llegada</label>
                                    <input type="date" name="fechaInicio" id="fechaInicio" required style="width: 100%; border: none; background: transparent; font-size: 14px; outline: none;" onchange="updateReservationSummary()" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div style="flex:1; padding: 0.75rem; border-left: 1px solid #ddd; position: relative;" class="date-picker-trigger">
                                    <label style="display:block; font-size: 10px; font-weight: 800; text-transform: uppercase;">Salida</label>
                                    <input type="date" name="fechaFin" id="fechaFin" required style="width: 100%; border: none; background: transparent; font-size: 14px; outline: none;" onchange="updateReservationSummary()" min="<?php echo date('Y-m-d'); ?>">
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
                                    <span style="text-decoration: underline;">Tarifa de limpieza</span> <span id="summaryCleaning">$0 MXN</span>
                                </li>
                                <li style="display:flex; justify-content: space-between; margin-bottom: 1rem;">
                                    <span style="text-decoration: underline;">Impuestos (16%)</span> <span id="summaryTax">$0 MXN</span>
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

    <!-- Gallery Modal -->
    <div id="galleryModal" class="gallery-modal">
        <div class="gallery-modal-header">
            <div class="gallery-counter"><span id="galCurrent">1</span> / <span id="galTotal">1</span></div>
            <button class="gallery-btn" onclick="closeGallery()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="gallery-modal-content" onclick="handleGalClick(event)">
            <button class="gallery-btn gallery-nav-btn gallery-prev" onclick="prevGal()"><i class="fa-solid fa-chevron-left"></i></button>
            <img id="galMainImg" src="" alt="Gallery Image">
            <button class="gallery-btn gallery-nav-btn gallery-next" onclick="nextGal()"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

    <script>
        // Variables del servidor — inyectadas para uso en detalle.js
        window.DETALLE_DATA = {
            idPropiedad:    <?php echo $idPropiedad; ?>,
            precioNoche:    <?php echo $prop['dPrecioNoche']; ?>,
            capacidadHuespedes: <?php echo $prop['iCapacidadHuespedes']; ?>,
            reservedRanges: <?php echo json_encode($reservedDates); ?>,
            specialRates:   <?php echo json_encode($specialRates); ?>,
            galleryImages:  <?php echo json_encode($images); ?>,
            totalReseniasHuesped: <?php echo $totalReseniasHuesped; ?>,
            currentUserId: <?php echo $_SESSION['idUsuario'] ?? 0; ?>,
            categoria: '<?php echo $prop['tipo']; ?>'
        };

        let currentGalIndex = 0;
        const galModal = document.getElementById('galleryModal');
        const galImg = document.getElementById('galMainImg');
        const galCurrent = document.getElementById('galCurrent');
        const galTotal = document.getElementById('galTotal');

        function openGallery(index) {
            currentGalIndex = index;
            updateGalleryUI();
            galModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeGallery() {
            galModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function updateGalleryUI() {
            const imgs = window.DETALLE_DATA.galleryImages;
            if (!imgs || imgs.length === 0) return;
            
            galImg.src = imgs[currentGalIndex];
            galCurrent.innerText = currentGalIndex + 1;
            galTotal.innerText = imgs.length;
        }

        function nextGal() {
            const imgs = window.DETALLE_DATA.galleryImages;
            currentGalIndex = (currentGalIndex + 1) % imgs.length;
            updateGalleryUI();
        }

        function prevGal() {
            const imgs = window.DETALLE_DATA.galleryImages;
            currentGalIndex = (currentGalIndex - 1 + imgs.length) % imgs.length;
            updateGalleryUI();
        }

        function handleGalClick(e) {
            if (e.target.id === 'galleryModal-content' || e.target.classList.contains('gallery-modal-content')) {
                closeGallery();
            }
        }

        document.addEventListener('keydown', (e) => {
            if (!galModal.classList.contains('active')) return;
            if (e.key === 'Escape') closeGallery();
            if (e.key === 'ArrowRight') nextGal();
            if (e.key === 'ArrowLeft') prevGal();
        });
    </script>
    <script src="../../recursos/js/huesped/detalle.js?v=<?php echo time(); ?>"></script>

</body>
</html>
