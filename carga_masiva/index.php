<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Clientes</title>
</head>
<body>
    <h1>Subir archivo CSV</h1>
    <form action="carga.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="archivo_csv" accept=".csv" required>
        <button type="submit">Subir archivo</button>
    </form>

    <?php
    // Aquí mostramos la grilla con los clientes cargados desde el CSV
    if (isset($_GET['archivo'])) {
        include('./lib/listar.php');
    }
    ?>
</body>
</html>
