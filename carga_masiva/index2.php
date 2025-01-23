<?php
// URL API
$url = "https://crm.bancodecomercio.com.ar/api/industries";

// token de autenticación
$token = "Bearer 2|5YHxGRXs4t3xKWwHZgMCT5B5wDW88KMfhwD4rdkVd487d346";

$ch = curl_init();

// Configurar las opciones de curl
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $token"
]);

// Ejecutar la solicitud
$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Comprobar si hubo errores de cURL
if (curl_errno($ch)) {
    echo 'Error en la solicitud: ' . curl_error($ch);
} else {
    echo "Código de estado HTTP: $httpCode\n";

    if ($httpCode == 200) {
        $data = json_decode($response, true);
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    } else {
        echo "Error en la solicitud. Código de estado HTTP: $httpCode\n";
        echo "Respuesta: $response\n";
    }
}

curl_close($ch);
