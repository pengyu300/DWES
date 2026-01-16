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

// Crear conexión
function conectarBD($servername, $username, $password, $dbname){
    try {
        // La sentencia de conexión
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Configurar atributos de PDO para manejar errores
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conn;
        
    } catch (PDOException $e) {

        echo "Error: " . $e->getMessage();
        // Terminar la ejecución del script si la conexión falla
        exit(); 
    }
}

function insertar_almacen($conn, $localidad){
    $stmt = $conn->prepare(
            "INSERT INTO almacen (localidad) VALUES (:localidad)"
        );

    $stmt->bindParam(':localidad', $localidad);
    $stmt->execute();
}

try {
    // Crear conexion
    $conn = conectarBD($servername, $username, $password, $dbname);

    // 1. Procesar el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $localidad = limpiar_campos($_POST['localidad']);

        // Insertar el nuevo almacén
        insertar_almacen($conn, $localidad);

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
