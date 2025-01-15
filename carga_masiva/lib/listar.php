<?php
include('db.php');

// Obtenemos los clientes cargados del CSV
$query = "SELECT * FROM clientes_pre";
$result = $conn->query($query);

echo "<form action='aprobar.php' method='POST'>";
echo "<table>";
echo "<thead><tr><th>Seleccionar</th><th>Nombre</th><th>Email</th><th>Teléfono</th></tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><input type='checkbox' name='clientes[]' value='" . $row['id'] . "'></td>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
echo "<button type='submit'>Aprobar Seleccionados</button>";
echo "</form>";
?>
