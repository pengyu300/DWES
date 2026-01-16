<!--Alta de Productos (comaltapro.php): dar de alta productos. Para seleccionar la categoría del
producto, se utilizará una lista de valores con los nombres de las categorías. El id_producto
será un campo con el formato Pxxxx donde xxxx será un número secuencial que comienza en
1 completándose con 0 hasta completar el formato (este campo será calculado desde PHP)-->

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

// Función para obtener categorías
function obtener_categorias($conn){
    $stmt = $conn->prepare(
        "SELECT id_categoria, nombre FROM categoria 
         ORDER BY id_categoria");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener el máximo id_producto actual
function generar_siguiente_id ($conn){
    $stmt = $conn->prepare(
        "SELECT MAX(id_producto) AS max_id FROM producto");

    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    $num = 1;
    if ($resultado['max_id']) {
        // Extrae el número (quita la 'P') y suma 1
        $num = intval(substr($resultado['max_id'], 1)) + 1;
    }

    return "P" . str_pad($num, 4, "0", STR_PAD_LEFT);
}

// Insertar producto
function insertar_producto($conn, $nombre, $precio, $id_categoria){
    $id_nuevo = generar_siguiente_id($conn);

     $stmt = $conn->prepare(
            "INSERT INTO producto (id_producto, nombre, precio, id_categoria) 
             VALUES (:id, :nombre, :precio, :id_categoria)"
        );

        $stmt->bindParam(':id', $id_nuevo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':id_categoria', $id_categoria);
        $stmt->execute();

        return $id_nuevo; // Devolver el ID para mostrarlo al usuario
}

$mensaje = "";
$categorias = [];

try{
    // Crear conexion
    $conn = conectarBD($servername, $username, $password, $dbname);

    // 1. Obtener categorias ANTES DEL FORMULARIO
    $categorias = obtener_categorias($conn);

    // 2. Procesar el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nombre = limpiar_campos($_POST['nombre']);
        $precio = limpiar_campos($_POST['precio']);
        $id_categoria = limpiar_campos($_POST['id_categoria']);

        // insertar
        $id_creado = insertar_producto($conn, $nombre, $precio, $id_categoria);

        $mensaje = "<p>Producto <strong>$nombre</strong> creado con ID <strong>$id_creado</strong></p>";
    }
} catch (PDOException $e) {
    $mensaje = "Error: " . $e->getMessage();
}

?>

<h1>Alta de productos</h1>

<!--Mostrar mensaje-->
<?= $mensaje ?>

<!-- Formulario -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label>Nombre del producto:</label>
    <input type="text" name="nombre" required><br><br>

    <label>Precio:</label>
    <input type="number" step="0.01" min="0" name="precio" required><br><br>

    <label>Categoría:</label>
    <select name="id_categoria" required>
        <option value=""> Selecciona una categoría </option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id_categoria'] ?>">
                <?= htmlspecialchars($cat['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Dar de alta producto">
</form>
