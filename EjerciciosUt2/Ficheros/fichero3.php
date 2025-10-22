<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichero 3</title>
    <style>
        table, th, td{
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px
        }
    </style>
</head>
<body>

<?php
$archivo = "C:/wamp64/www/pengyu/files/alumnos1.txt";

// Función para limpiar campos
function limpiar_campos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Verificar que el fichero existe
if (file_exists($archivo)){

    // Leer todas las lineas en un array
    $lineas = file($archivo);
    $cont = 0;

    echo "<h3>Listado de alumnos</h3>";
    echo "<table>";
    echo "<tr><th>Nombre</th><th>Apellido1</th><th>Apellido2</th><th>Fecha Nacimiento</th><th>Localidad</th></tr>";

    // Recorre cada linea del fichero
    foreach ($lineas as $linea){

        // Extraer campos según posiciones
        $nombre = limpiar_campos(substr($linea, 0, 40));
        $apellido1 = limpiar_campos(substr($linea, 40, 41));
        $apellido2 = limpiar_campos(substr($linea, 81, 42));
        $fecha_nac = limpiar_campos(substr($linea,123, 10));
        $localidad = limpiar_campos(substr($linea, 133, 27));

        echo "<tr>";
        echo "<td>$nombre</td>";
        echo "<td>$apellido1</td>";
        echo "<td>$apellido2</td>";
        echo "<td>$fecha_nac</td>";
        echo "<td>$localidad</td>";
        echo "</tr>";

        $cont++;

    }

    echo "</table>";
    echo "<p><strong>Número total de alumnos leídos: $cont</strong></p>";
} 
// Si no existe el fichero
else {
    echo "<p>No se encontró el fichero alumnos1.txt.</p>";
}



?>
    
</body>
</html>
