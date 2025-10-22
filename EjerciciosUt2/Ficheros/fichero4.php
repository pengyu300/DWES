<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichero 4</title>
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
$archivo = "C:/wamp64/www/pengyu/files/alumnos2.txt";

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
    echo "<tr>
            <th>Nombre</th>
            <th>Apellido1</th>
            <th>Apellido2</th>
            <th>Fecha Nacimiento</th>
            <th>Localidad</th></tr>";

    // Recorre cada linea del fichero
    foreach ($lineas as $linea){
        // Dividir los campos usando el ##
        $campos = explode("##", $linea);

        // Limpiar cada campo
        // Si la variable existe y no es null, usa su valor
        // Si no existe o es null, usa el valor que está después del ??
        $nombre = limpiar_campos($campos[0] ?? "");
        $apellido1 = limpiar_campos($campos[1] ?? "");
        $apellido2 = limpiar_campos($campos[2] ?? "");
        $fecha_nac = limpiar_campos($campos[3] ?? "");
        $localidad = limpiar_campos($campos[4] ?? "");

        // Mostrar en la tabla
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
    echo "<p>No se encontró el fichero alumnos2.txt.</p>";
}



?>
    
</body>
</html>
