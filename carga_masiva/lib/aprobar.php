<?php
include('db.php');

// Recibimos los IDs de los clientes aprobados
if (isset($_POST['clientes'])) {
    $clientes = $_POST['clientes'];

    // Conexión a la API del CRM (asumimos que tienes la URL y las credenciales de la API)
    $api_url = "https://api.concordcrm.com/v1/clientes";
    $api_key = "TU_API_KEY";

    foreach ($clientes as $cliente_id) {
        // Obtenemos los datos del cliente desde la base de datos
        $query = "SELECT * FROM clientes_pre WHERE id = $cliente_id";
        $result = $conn->query($query);
        $cliente = $result->fetch_assoc();

        // Preparamos los datos para la API
        $data = [
            'nombre' => $cliente['nombre'],
            'dominio_empresa' => $cliente['dominio_empresa'],
            'email' => $cliente['email'],
            'sector' => $cliente['sector'],
            'telefono' => $cliente['telefono'],
            'tags' => $cliente['tags'],
            'direccion' => $cliente['direccion'],
            'ciudad' => $cliente['ciudad'],
            'estado_region' => $cliente['estado_region'],
            'codigo_postal' => $cliente['codigo_postal'],
            'propietario' => $cliente['propietario'],
            'nota' => $cliente['nota'],
            'cuit_pj' => $cliente['cuit_pj'],
            'contactos' => $cliente['contactos'],
        ];

        // Enviar a la API usando cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Verificamos que el código de respuesta sea 200
        if ($status_code == 200) {
            // Guardamos en el log
            $log_query = "INSERT INTO logs (cantidad, propietario, archivo) VALUES (1, ?, ?)";
            $stmt = $conn->prepare($log_query);
            $stmt->bind_param('ss', $cliente['propietario'], 'clientes.csv');
            $stmt->execute();
            $stmt->close();
        }
    }

    // Actualizamos el estado de los clientes aprobados
    $cliente_ids = implode(',', $clientes);
    $update_query = "UPDATE clientes_pre SET aprobado = 1 WHERE id IN ($cliente_ids)";
    $conn->query($update_query);

    // Redirigimos al usuario con un mensaje de éxito
    echo "<script>alert('Clientes cargados exitosamente'); window.location.href = 'index.php';</script>";
}
?>
