<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carga Masiva CRM</title>
    <!-- cdn tailwind css -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Carga Masiva de Clientes CRM</h1>

        <!-- Formulario de carga -->

        <form id="uploadForm" class="mb-8" enctype="multipart/form-data">
            <div class="flex flex-col space-y-4">
                <div class="flex items-center space-x-4">
                    <input type="text"
                        name="firstName"
                        placeholder="Nombre"
                        class="px-4 py-2 border rounded"
                        required>
                    <input type="text"
                        name="lastName"
                        placeholder="Apellido"
                        class="px-4 py-2 border rounded"
                        required>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="file"
                        name="csvFile"
                        accept=".csv"
                        class="px-4 py-2 border rounded">
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded hover:bg-blue-600">
                        Cargar CSV
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabla de visualización -->
        <div id="previewTable" class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"></thead>
                <tbody class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>

        <!-- Botones de acción -->
        <div id="actionButtons" class="mt-4 space-x-4 hidden">
            <button id="selectAll" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Seleccionar Todos
            </button>
            <button id="approveUpload" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Aprobar Carga
            </button>
        </div>

        <!-- Alertas -->
        <div id="alertContainer" class="mt-4"></div>
    </div>

    <script>
        let selectedRows = {};
        let csvData = [];
        let fileName = '';

        $(document).ready(function() {
            // Manejar carga de archivo
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fileName = $('input[type="file"]').val().split('\\').pop();

                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.error) {
                                showAlert(data.error, 'error');
                            } else {
                                csvData = data;
                                renderTable(data);
                                $('#actionButtons').removeClass('hidden');
                            }
                        } catch (e) {
                            showAlert('Error al procesar la respuesta del servidor', 'error');
                        }
                    },
                    error: function() {
                        showAlert('Error al procesar el archivo', 'error');
                    }
                });
            });

            // Seleccionar todos
            $('#selectAll').click(function() {
                const isAllSelected = Object.keys(selectedRows).length === csvData.length;
                selectedRows = isAllSelected ? {} :
                    csvData.reduce((acc, row, index) => {
                        acc[index] = true;
                        return acc;
                    }, {});
                updateTableSelection();
            });

            // Aprobar carga
            $('#approveUpload').click(function() {
                if (confirm('¿Está seguro que desea cargar los clientes seleccionados?')) {
                    const selectedData = csvData.filter((_, index) => selectedRows[index]);

                    $.ajax({
                        url: 'upload.php',
                        type: 'POST',
                        data: {
                            data: JSON.stringify(selectedData),
                            fileName: fileName
                        },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.status === 'success') {
                                    showAlert('Clientes cargados exitosamente. Código: 200', 'success');
                                } else {
                                    showAlert(result.message || 'Error al cargar los clientes', 'error');
                                }
                            } catch (e) {
                                showAlert('Error al procesar la respuesta del servidor', 'error');
                            }
                        },
                        error: function() {
                            showAlert('Error en la carga', 'error');
                        }
                    });
                }
            });
        });

        // El resto de las funciones (renderTable, toggleRow, updateTableSelection, showAlert) permanecen igual...
    </script>
</body>

</html>