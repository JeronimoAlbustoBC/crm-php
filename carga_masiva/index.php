<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Clientes</title>
    <script>
        // funcion para marcar todas las filas
        function checkAll() {
            let checkboxes = document.querySelectorAll('.checkbox_client');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
        }

        // funcion para desmarcar todas las filas
        function unCheckAll() {
            let checkboxes = document.querySelectorAll('.checkbox_client');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
        }
    </script>
    <style>
        /* Alinear los botones en una sola fila */
        .botones-marcado {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body>
    <h1>Cargar Clientes</h1>
    <form id="form" method="POST" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="lastname">Lastname:</label>
        <input type="text" name="lastname" required>
        <label for="csvFile">Seleccionar archivo CSV:</label>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
        <br><br>
        <input type="submit" name="submitFile" value="Subir CSV">
    </form>

    <?php
    // Función para verificar si el archivo .CSV es correcto
    function validateCSV($file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mimeType === 'text/plain' || $mimeType === 'application/vnd.ms-excel';
    }

    if (isset($_POST['submitFile'])) {
        // Validación del archivo .CSV
        if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] != 0) {
            echo "<script>alert('Error al subir el archivo. Intente de nuevo.');</script>";
            exit;
        }

        // Comprobar si el archivo es un .CSV correcto
        $file = $_FILES['csvFile']['tmp_name'];
        if (!validateCSV($file)) {
            echo "<script>alert('El archivo no es válido. Asegúrese de subir un archivo CSV.');</script>";
            exit;
        }

        // Procesar el archivo .CSV
        $csv = file_get_contents($file);
        $rows = explode("\n", $csv);
        $clients = [];

        // Saltar la primera fila (encabezado- titulo)
        array_shift($rows);

        foreach ($rows as $row) {
            // se asegura de no procesar filas vacías
            if (trim($row) != "") {
                $clients[] = str_getcsv($row, ',');  // .CSV con separador por ,
            }
        }

        // Mostrar la tabla con los clientes
        if (count($clients) > 0) {
            echo "<h2>Clientes Cargados</h2>";
            echo "<form method='POST'>";
            echo "<input type='hidden' name='name' value='" . htmlspecialchars($_POST['name']) . "'>";
            echo "<input type='hidden' name='lastname' value='" . htmlspecialchars($_POST['lastname']) . "'>";
            echo "<table border='1'>";
            echo "<thead>
            <tr>
            <th>select</th>
            <th>company name</th>
            <th>domain name</th>
            <th>email</th>
            <th>industry</th>
            <th>phone</th>
            <th>tags</th>
            <th>address</th>
            <th>city</th>
            <th>state</th>
            <th>postal code</th>
            <th>owner</th>
            <th>note</th>
            <th>cuit pj</th>
            <th>contact</th>
            </tr></thead>
            <tbody>";

            foreach ($clients as $index => $client) {
                echo "<tr><td><input type='checkbox' class='checkbox_client' name='clients[]' value='$index'></td>";
                echo "<td>" . htmlspecialchars($client[1]) . "</td>"; // company_name
                echo "<td>" . htmlspecialchars($client[2]) . "</td>"; // domain_name
                echo "<td>" . htmlspecialchars($client[3]) . "</td>"; // email
                echo "<td>" . htmlspecialchars($client[4]) . "</td>"; // industry
                echo "<td>" . htmlspecialchars($client[5]) . "</td>"; // phone
                echo "<td>" . htmlspecialchars($client[6]) . "</td>"; // tags
                echo "<td>" . htmlspecialchars($client[7]) . "</td>"; // address
                echo "<td>" . htmlspecialchars($client[8]) . "</td>"; // city
                echo "<td>" . htmlspecialchars($client[9]) . "</td>"; // state
                echo "<td>" . htmlspecialchars($client[10]) . "</td>"; // postal code
                echo "<td>" . htmlspecialchars($client[11]) . "</td>"; // owner
                echo "<td>" . htmlspecialchars($client[12]) . "</td>"; // note
                echo "<td>" . htmlspecialchars($client[13]) . "</td>"; // cuit pj
                echo "<td>" . htmlspecialchars($client[14]) . "</td>"; // contact
                echo "</tr>";
            }

            echo "</tbody></table>";

            // Botones de marcar/desmarcar clientes
            echo "<div class='botones-marcado'>
                    <input type='button' value='Marcar Todos' onclick='checkAll()'>
                    <input type='button' value='Desmarcar Todos' onclick='unCheckAll()'>
                  </div>";

            echo "<br><br><input type='submit' name='loadClients' value='Cargar Clientes'>";
            echo "</form>";
        } else {
            echo "<script>alert('El archivo no contiene datos válidos.');</script>";
        }
    }
    ?>

<?php
if (isset($_POST['loadClients'])) {
    $clientSelec = isset($_POST['clients']) ? $_POST['clients'] : [];
    $name = htmlspecialchars($_POST['name']);      // Nombre del usuario
    $lastname = htmlspecialchars($_POST['lastname']); // Apellido del usuario

    if (!empty($clientSelec) && is_array($clientSelec)) {
        // Crear directorio de logs si no existe
        $logDirectory = 'logs/' . date('Y/m');
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        // Crear el archivo de log con la fecha y hora actual
        $logFile = $logDirectory . '/log_' . date('d-m-Y_H-i-s') . '.txt';
        $logContent = "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $logContent .= "Cantidad de registros: " . count($clientSelec) . "\n";
        $logContent .= "Propietario: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
        $logContent .= "Nombre: " . $name . "\n";
        $logContent .= "Apellido: " . $lastname . "\n\n";

        // Iterar sobre los clientes seleccionados
        foreach ($clientSelec as $index) {
            $client = $clients[$index]; // Obtener la fila del cliente correspondiente
            
            // Aquí agregamos los detalles de cada cliente seleccionado al archivo de log
            $logContent .= "Nombre de la Empresa: " . htmlspecialchars($client[0]) . "\n"; // company_name
            $logContent .= "Dominio: " . htmlspecialchars($client[1]) . "\n"; // domain_name
            $logContent .= "Email: " . htmlspecialchars($client[2]) . "\n"; // email
            $logContent .= "Sector: " . htmlspecialchars($client[3]) . "\n"; // industry
            $logContent .= "Teléfono: " . htmlspecialchars($client[4]) . "\n"; // phone
            $logContent .= "Tags: " . htmlspecialchars($client[5]) . "\n"; // tags
            $logContent .= "Dirección: " . htmlspecialchars($client[6]) . "\n"; // address
            $logContent .= "Ciudad: " . htmlspecialchars($client[7]) . "\n"; // city
            $logContent .= "Estado/Región: " . htmlspecialchars($client[8]) . "\n"; // state
            $logContent .= "Código Postal: " . htmlspecialchars($client[9]) . "\n"; // postal code
            $logContent .= "Propietario: " . htmlspecialchars($client[10]) . "\n"; // owner
            $logContent .= "Nota: " . htmlspecialchars($client[11]) . "\n"; // note
            $logContent .= "CUIT PJ: " . htmlspecialchars($client[12]) . "\n"; // cuit pj
            $logContent .= "Contactos: " . htmlspecialchars($client[13]) . "\n\n"; // contact
        }

        // Intentar escribir el contenido en el archivo
        if (file_put_contents($logFile, $logContent) !== false) {
            echo "<script>alert('Subida de clientes exitosa. Código 200 OK');</script>";
        } else {
            echo "<script>alert('Error al escribir el archivo de log.');</script>";
        }
    } else {
        echo "<script>alert('No se seleccionaron clientes para cargar o los datos no son válidos.');</script>";
    }
}
?>


</body>

</html>
