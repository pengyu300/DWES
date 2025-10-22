<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichero 2</title>
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
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label>Nombre:</label>
    <input type="text" name="nombre" maxlength="40" required><br><br>

    <label>Apellido1:</label>
    <input type="text" name="apellido1" maxlength="41" required><br><br>

    <label>Apellido2:</label>
    <input type="text" name="apellido2" maxlength="42" required><br><br>

    <label>Fecha de nacimiento:</label>
    <input type="date" name="fecha_nac" required><br><br>

    <label>Localidad:</label>
    <input type="text" name="localidad" maxlength="27" required><br><br>

    <input type="submit" value="Guardar">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y limpiar los datos del formulario
    $nombre = limpiar_campos($_POST["nombre"]);
    $apellido1 = limpiar_campos($_POST["apellido1"]);
    $apellido2 = limpiar_campos($_POST["apellido2"]);
    $fecha_nac = limpiar_campos($_POST["fecha_nac"]);
    $localidad = limpiar_campos($_POST["localidad"]);

    // Crear línea separada por ##
    $linea = $nombre . "##" . $apellido1 . "##" . $apellido2 . "##" . $fecha_nac . "##" . $localidad . "\r\n";

    // Abrir el fichero en modo append que lo añade al final
    $fichero = fopen($archivo, "a");

    if ($fichero) {
        fwrite($fichero, $linea);
        fclose($fichero);
        echo "Alumno guardado correctamente. </br>";
    } else {
        echo "Error al abrir el fichero. </br>";
    }
}
?>
</body>
</html>
