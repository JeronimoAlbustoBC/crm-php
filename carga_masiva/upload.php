<?php
if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
    $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : 'clientes.txt';

    $file = fopen($fileName, 'a');
    if ($file) {
        foreach ($data as $client) {
            fwrite($file, "{$client['firstName']} {$client['lastName']}\n");
        }
        fclose($file);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar los datos']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos']);
}
?>
