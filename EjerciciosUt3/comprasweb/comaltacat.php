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
    // Crear conexion
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Activar modo de errores con excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // si el formulario se ha enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = limpiar_campos($_POST['nombre']);

        // Buscar el último ID_categoria existente (el mas alto)
        $stmt = $conn->prepare("select max(id_categoria) as ultimo from categoria");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultado = $stmt->fetch(); // devuelve solo una fila asociativa


        // calcular el siguiente numero
        $num = 1; // si no hay categorias, empezar por 1
        if ($resultado["ultimo"]){
            // Quitar "C-" del ID y convertir a número  Ej: C-003 -> 3
            $num = intval(substr($resultado["ultimo"], 2))+1;
        }

        // Crear el nuevo id con el formato C-xxx
        // 3: Longitud total deseada, 0: Carácter de relleno, STR_PAD_LEFT: Dirección del relleno
        $id_nuevo = "C-" . str_pad($num, 3, "0", STR_PAD_LEFT);

        // sentencia para insertar la nueva categoria
        $stmt = $conn->prepare("INSERT INTO categoria (id_categoria, nombre) VALUES (:id, :nombre)");
        $stmt->bindParam(':id', $id_nuevo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->execute();

        // Mostrar mensaje
        echo "<p>Categoría $nombre creada con ID <strong>$id_nuevo</strong></p>";

    }

}

// Si ocurre un error de conexión o SQL, se muestra mensaje
catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
// Cerrar la conexión
$conn = null;

?>


<!-- Formulario -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<label>Nombre de la categoría:</label>
  <input type="text" name="nombre" required>
  <input type="submit" value="Dar de alta">
</form>
