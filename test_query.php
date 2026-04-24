<?php
$mysqli = new mysqli('localhost', 'root', '', 'estancias_digitales');
$res = $mysqli->query("SHOW TABLES LIKE 'tbl_estado_reserva'");
if ($res->num_rows > 0) {
    $res2 = $mysqli->query("SELECT * FROM tbl_estado_reserva");
    while($row = $res2->fetch_assoc()) { print_r($row); }
} else {
    echo "Table does not exist";
}
?>
