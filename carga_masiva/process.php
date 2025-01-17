<?php
// Verifica si se subió el archivo llamado 'csvFile'
if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    // Obtiene ubicación y nombre del archivo
    $file = $_FILES['csvFile']['tmp_name'];
    
    // Abre el archivo en modo lectura
    if (($handle = fopen($file, "r")) !== FALSE) {
        $data = [];
        // Lee la primera línea del archivo, que es el encabezado (se puede omitir si no es necesario)
        $header = fgetcsv($handle);
        
        // Verifica si el encabezado tiene datos, de lo contrario, termina el proceso
        if ($header === FALSE) {
            echo json_encode(['error' => 'El archivo CSV está vacío o no tiene encabezado.']);
            fclose($handle);
            exit;
        }

        // Lee las filas del archivo una por una
        while (($row = fgetcsv($handle)) !== FALSE) {
            // Verifica que la fila no esté vacía y tenga la cantidad esperada de columnas
            if (count($row) >= 14) {
                // Construye un arreglo asociativo para cada fila
                $data[] = [
                    'company_name' => isset($row[0]) ? $row[0] : '',   // Nombre de la compañía - posición 0
                    'domain_name' => isset($row[1]) ? $row[1] : '',    // Dominio
                    'email' => isset($row[2]) ? $row[2] : '',          // Correo electrónico
                    'industry' => isset($row[3]) ? $row[3] : '',       // Industria
                    'phone' => isset($row[4]) ? $row[4] : '',          // Teléfono
                    'tags' => isset($row[5]) ? $row[5] : '',           // Etiquetas
                    'address' => isset($row[6]) ? $row[6] : '',        // Dirección
                    'city' => isset($row[7]) ? $row[7] : '',           // Ciudad
                    'state' => isset($row[8]) ? $row[8] : '',          // Estado
                    'zip_code' => isset($row[9]) ? $row[9] : '',       // Código postal
                    'owner' => isset($row[10]) ? $row[10] : '',         // Propietario
                    'note' => isset($row[11]) ? $row[11] : '',          // Nota
                    'cuit_pj' => isset($row[12]) ? $row[12] : '',       // CUIT PJ
                    'contact' => isset($row[13]) ? $row[13] : '',       // Contacto
                ];
            }
        }

        // Cierra el archivo
        fclose($handle);

        // Si hay datos, los devuelve en formato JSON
        if (!empty($data)) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'No se encontraron datos válidos en el archivo CSV.']);
        }
    } else {
        echo json_encode(['error' => 'No se pudo abrir el archivo CSV.']);
    }
} else {
    echo json_encode(['error' => 'No se subió ningún archivo o ocurrió un error en la subida.']);
}
?>
