<?php
// URL API
$url = "https://crm.bancodecomercio.com.ar/api/users";

// token de autenticación
$token = "Bearer 2|5YHxGRXs4t3xKWwHZgMCT5B5wDW88KMfhwD4rdkVd487d346";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => "https://crm.bancodecomercio.com.ar/api/users?=",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "",
    CURLOPT_HTTPHEADER => [
        "Accept: applicpair_0831c86631fc464c850aa6be3f418b14ation/json",
        "Authorization: Bearer 2|5YHxGRXs4t3xKWwHZgMCT5B5wDW88KMfhwD4rdkVd487d346"
    ],
]);


// Configurar las opciones de curl
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
// curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     "Authorization: $token"
// ]);


// Ejecutar la solicitud
$response = curl_exec($ch);

// Comprobar errores de cURL
if (curl_errno($ch)) {
    echo 'Error en la solicitud: ' . curl_error($ch);
} else {
    // Verificar el código de estado HTTP
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Código de estado HTTP: $httpCode\n";

    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data) {
            print_r($data);
        } else {
            echo "No se pudo decodificar la respuesta JSON.\n";
        }
    } else {
        echo "Error en la solicitud. Código de estado HTTP: $httpCode\n";
        echo "Respuesta: $response\n";
    }
}

curl_close($ch);
