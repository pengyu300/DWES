<!-- Alta de Categorías (comaltacat.php): dar de alta categorías de productos. -->
<?php

$servername = "localhost";
$username = "root";
$password = "rootroot";
$dbname = "COMPRASWEB";


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

// Buscar el ultimo id_categoria existente
function buscarID($conn){
  // Buscar el último ID_categoria existente (el mas alto)
  $stmt = $conn->prepare(
    "SELECT max(id_categoria) as ultimo 
     from categoria"
  );

  $stmt->execute();

  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $resultado = $stmt->fetch(); // devuelve solo una fila asociativa

  return $resultado;
}

//función para insertar la nueva categoria
function insertarCategoria ($conn, $id_categoria, $nombre){
  $stmt = $conn->prepare(
    "INSERT INTO categoria (id_categoria, nombre) 
     VALUES (:id_categoria, :nombre)");

    $stmt->bindParam(':id_categoria', $id_categoria);
    $stmt->bindParam(':nombre', $nombre);

    $stmt->execute();
}


try {
    // Crear conexion
    $conn = conectarBD($servername, $username, $password, $dbname);

    // si el formulario se ha enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = limpiar_campos($_POST['nombre']);

        // Buscar el último ID_categoria existente (el mas alto)
        $buscar = buscarID($conn);

        // calcular el siguiente numero
        $num = 1; // si no hay categorias, empezar por 1
        if ($buscar["ultimo"]){
            // Quitar "C-" del ID y convertir a número  Ej: C-003 -> 3
            $num = intval(substr($buscar["ultimo"], 2))+1;
        }

        // Crear el nuevo id con el formato C-xxx
        // 3: Longitud total deseada, 0: Carácter de relleno, STR_PAD_LEFT: Dirección del relleno
        $id_categoria = "C-" . str_pad($num, 3, "0", STR_PAD_LEFT);

        // sentencia para insertar la nueva categoria
        insertarCategoria($conn, $id_categoria, $nombre);

        // Mostrar mensaje
        echo "<p>Categoría $nombre creada con ID <strong>$id_categoria</strong></p>";
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
