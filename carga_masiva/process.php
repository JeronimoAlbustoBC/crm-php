<?php
if (isset($_FILES['csvFile'])) {
    $file = $_FILES['csvFile']['tmp_name'];
    $fileName = $_FILES['csvFile']['name'];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        $data = [];
        $header = fgetcsv($handle); // Asumimos que el primer row contiene los encabezados.
        
        while (($row = fgetcsv($handle)) !== FALSE) {
            // Aquí mapeamos cada columna del CSV a los campos correspondientes
            $data[] = [
                'company_name' => $row[0],   // Nombre de la compañía
                'domain_name' => $row[1],    // Dominio
                'email' => $row[2],          // Correo electrónico
                'industry' => $row[3],       // Industria
                'phone' => $row[4],          // Teléfono
                'tags' => $row[5],           // Etiquetas
                'address' => $row[6],        // Dirección
                'city' => $row[7],           // Ciudad
                'state' => $row[8],          // Estado
                'zip_code' => $row[9],       // Código postal
                'owner' => $row[10],         // Propietario
                'note' => $row[11],          // Nota
                'cuit_pj' => $row[12],       // CUIT PJ
                'contact' => $row[13],       // Contacto
            ];
        }

        fclose($handle);
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'No se pudo leer el archivo CSV.']);
    }
} else {
    echo json_encode(['error' => 'No se subió ningún archivo.']);
}
?>
