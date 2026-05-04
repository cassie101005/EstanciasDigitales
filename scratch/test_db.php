<?php
$conexion = new mysqli("localhost", "root", "", "estancias_digitales");
$idHost = 1; // Assuming 1 is the host ID, but let's just get the raw query result.
$sql = "SELECT c.dPenalizacion FROM tbl_cancelacion c JOIN tbl_reserva r ON c.idReserva = r.idReserva JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad WHERE p.idUsuario = 2"; // Try ID 2 as well, since user ID might be 2 or 3.
$res = $conexion->query($sql);
while($row = $res->fetch_assoc()) {
    echo "Penalty for host 2: " . $row['dPenalizacion'] . "<br>";
}
$sql = "SELECT c.dPenalizacion FROM tbl_cancelacion c JOIN tbl_reserva r ON c.idReserva = r.idReserva JOIN tbl_propiedad p ON r.idPropiedad = p.idPropiedad WHERE p.idUsuario = 3"; // Try ID 3.
$res = $conexion->query($sql);
while($row = $res->fetch_assoc()) {
    echo "Penalty for host 3: " . $row['dPenalizacion'] . "<br>";
}
?>
