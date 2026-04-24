<?php
$mysqli = new mysqli('localhost', 'root', '', 'estancias_digitales');
$res = $mysqli->query("SELECT * FROM tbl_cancelacion");
while($row = $res->fetch_assoc()) { print_r($row); }
?>
