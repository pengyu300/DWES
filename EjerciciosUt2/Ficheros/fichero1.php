<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fichero 1</title>
</head>
<body>
<?php
$archivo = "C:/wamp64/www/files/alumnos1.txt";

// Función para rellenar con espacios
function rellenar($texto, $longitud){
    $texto = substr($texto, 0, $longitud);
    return str_pad($texto, $longitud, " ");
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
    $nombre = $_POST["nombre"];
    $apellido1 = $_POST["apellido1"];
    $apellido2 = $_POST["apellido2"];
    $fecha_nac = $_POST["fecha_nac"];
    $localidad = $_POST["localidad"];

    // Crear línea con posiciones fijas
    $linea = rellenar($nombre, 40)
           . rellenar($apellido1, 41)
           . rellenar($apellido2, 42)
           . rellenar($fecha_nac, 10)
           . rellenar($localidad, 27)
           . "\r\n"; // salto de línea Windows

    // Abrir el fichero en modo append
    $fichero = fopen($archivo, "a");

    if ($fichero) {
        fwrite($fichero, $linea);
        fclose($fichero);
        echo "Alumno guardado correctamente.";
    } else {
        echo "Error al abrir el fichero.";
    }
}
?>
</body>
</html>
