<!--Alta de Almacenes (comaltaalm.php): dar de alta almacenes en diferentes localidades. El
número de almacén será un número secuencial.-->

<?php

$servername = "localhost";
$username = "root";
$password = "rootroot";
$dbname = "comprasweb";

function limpiar_campos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Crear conexión
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Procesar el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $localidad = limpiar_campos($_POST['localidad']);

        // Insertar el nuevo almacén
        $stmt = $conn->prepare(
            "INSERT INTO almacen (localidad) VALUES (:localidad)"
        );
        $stmt->bindParam(':localidad', $localidad);
        $stmt->execute();

        // Obtener numero id autogenerado
        $nuevo_id = $conn->lastInsertId();

        echo "<p>Almacén creado correctamente.<br> 
              Número de almacén: <strong>$nuevo_id</strong><br>
              Localidad: <strong>$localidad</strong></p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;

?>

<!-- Formulario -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

    <label>Localidad del almacén:</label>
    <input type="text" name="localidad" required><br><br>

    <input type="submit" value="Dar de alta almacén">

</form>
