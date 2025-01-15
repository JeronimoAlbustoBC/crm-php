<?php
$fecha = date('Y-m');
$directorio = "logs/$fecha";
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

$log_file = "$directorio/log_" . time() . ".txt";
file_put_contents($log_file, "Clientes cargados: " . implode(", ", $clientes) . "\n", FILE_APPEND);
