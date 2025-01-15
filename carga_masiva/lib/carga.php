<?php
if (isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    // Abrimos el archivo CSV
    $file = fopen($archivo, 'r');

    // Saltamos la primera fila (encabezado)
    fgetcsv($file, 1000, ";");

    // Conectamos con la base de datos
    include('db.php');
    
    // Preparamos la consulta para insertar los datos
    $stmt = $conn->prepare("INSERT INTO clientes_pre (nombre, dominio_empresa, email, sector, telefono, tags, direccion, ciudad, estado_region, codigo_postal, propietario, nota, cuit_pj, contactos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Leemos el CSV y almacenamos los registros en la base de datos
    while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
        $stmt->bind_param('ssssssssssssss', $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12]);
        $stmt->execute();
    }

    fclose($file);
    $stmt->close();
    
    // Redirigimos a la página de visualización
    header("Location: index.php?archivo=" . basename($_FILES['archivo_csv']['name']));
}
?>
