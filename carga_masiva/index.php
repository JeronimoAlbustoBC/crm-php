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
    <?php header('Content-Type: text/html; charset=UTF-8'); ?>
    <form id="form" method="POST" enctype="multipart/form-data">
        <label for="user">usuario:</label>
        <select name="user" id="user" required>
            <!-- Las opciones se llenan con los datos de la API de usuarios -->
            <?php

            //  Key para la autenticación de la API de usuarios
            $api_key = "2|5YHxGRXs4t3xKWwHZgMCT5B5wDW88KMfhwD4rdkVd487d346";

            // Función para hacer una llamada a la API de usuarios con cURL
            function getUsers($api_key)
            {
                $url = 'https://crm.bancodecomercio.com.ar/api/users';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Authorization: Bearer $api_key"
                ));

                // Ejecutar la solicitud y obtener la respuesta de la API
                $response = curl_exec($ch);



                // Verificar si hubo un error en la solicitud a la API
                if (curl_errno($ch)) {
                    echo "Error en cURL: " . curl_error($ch);
                    curl_close($ch);
                    return null; 
                }

                curl_close($ch);

                // Decodifica la respuesta de la API a JSON
                $data = json_decode($response, true);

                // Verificar si la decodificación fue exitosa y si contiene los datos esperados
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "Error al decodificar JSON: " . json_last_error_msg();
                    return null;
                }

                return $data; // devuelve los datos decodificados
            }

            // Cargar los usuarios de la funcion getUsers
            $users = getUsers($api_key);

            if ($users && isset($users['data']) && count($users['data']) > 0) {
                foreach ($users['data'] as $user) {
                    echo "<option value='" . $user['id'] . "'>" . $user['name'] . "</option>";
                }
            } else {
                echo "<option value=''>No se pudo cargar los usuarios o no hay usuarios disponibles</option>";
            }
            ?>

        </select>

        <label for="source">origen:</label>
        <select name="source" id="source" required>
            <!-- Las opciones se llenarán con los datos de la API de fuente/origen -->

            <?php
            // Key para la autenticación de la API de fuente/origen
            $api_key = "2|5YHxGRXs4t3xKWwHZgMCT5B5wDW88KMfhwD4rdkVd487d346";

            // Función para hacer una llamada a la API de fuente con cURL
            function getSource($api_key)
            {
                $url = 'https://crm.bancodecomercio.com.ar/api/sources';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Authorization: Bearer $api_key"
                ));

                // Ejecutar la solicitud y obtener la respuesta de la API
                $response = curl_exec($ch);

                // Verificar si hubo un error en la solicitud
                if (curl_errno($ch)) {
                    echo "Error en cURL: " . curl_error($ch);
                    curl_close($ch);
                    return null;
                }

                curl_close($ch);

                // Decodificar la respuesta de la API a JSON
                $data = json_decode($response, true);

                // Verificar si la decodificación fue exitosa y si contiene datos esperados
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "Error al decodificar JSON: " . json_last_error_msg();
                    return null;
                }

                return $data; // Retorna los datos decodificados
            }

            // Cargar la fuente (origen) de la funcion getSource
            $sources = getSource($api_key);

            if ($sources && isset($sources['data']) && count($sources['data']) > 0) { // Verificar que 'data' no esté vacío
                foreach ($sources['data'] as $source) {
                    echo "<option value='" . $source['id'] . "'>" . $source['name'] . "</option>";
                }
            } else {
                echo "<option value=''>No se pudo cargar las fuentes o no hay fuentes disponibles</option>";
            }
            ?>

        </select>

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
                echo "<td>" . htmlspecialchars($client[0]) . "</td>"; // company_name
                echo "<td>" . htmlspecialchars($client[1]) . "</td>"; // domain_name
                echo "<td>" . htmlspecialchars($client[2]) . "</td>"; // email
                echo "<td>" . htmlspecialchars($client[3]) . "</td>"; // industry
                echo "<td>" . htmlspecialchars($client[4]) . "</td>"; // phone
                echo "<td>" . htmlspecialchars($client[5]) . "</td>"; // tags
                echo "<td>" . htmlspecialchars($client[6]) . "</td>"; // address
                echo "<td>" . htmlspecialchars($client[7]) . "</td>"; // city
                echo "<td>" . htmlspecialchars($client[8]) . "</td>"; // state
                echo "<td>" . htmlspecialchars($client[9]) . "</td>"; // postal code
                echo "<td>" . htmlspecialchars($client[10]) . "</td>"; // owner
                echo "<td>" . htmlspecialchars($client[11]) . "</td>"; // note
                echo "<td>" . htmlspecialchars($client[12]) . "</td>"; // cuit pj
                echo "<td>" . htmlspecialchars($client[13]) . "</td>"; // contact
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
        $userId = $_POST['user'];
        $originId = $_POST['source'];

        var_dump($userId);
        var_dump($originId);


        if (!empty($clientSelec) && is_array($clientSelec)) {
            // Crear directorio de logs si no existe
            $logDirectory = 'logs/' . date('Y/m');
            if (!file_exists($logDirectory)) {
                mkdir($logDirectory, 0777, true); // Crea la carpeta si no existe
            }

            // Crear el archivo de log con la fecha y hora actual
            $logFile = $logDirectory . '/log_' . date('d-m-Y_H-i-s') . '.txt';
            $logContent = "Fecha: " . date('Y-m-d H:i:s') . "\n";
            $logContent .= "Cantidad de registros: " . count($clientSelec) . "\n";
            $logContent .= "Propietario: " . $_SERVER['REMOTE_ADDR'] . "\n\n"; // IP del propietario
            $logContent .= "Usuario (ID): " . $userId . "\n";  // ID del usuario seleccionado
            $logContent .= "Origen (ID): " . $originId . "\n";  // ID del origen seleccionado
            


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