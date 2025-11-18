<!-- Formulario -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label>Nombre del producto:</label>
    <input type="text" name="nombre" required><br><br>

    <label>Precio:</label>
    <input type="number" step="0.01" min="0" name="precio" required><br><br>

    <label>Categoría:</label>
    <select name="id_categoria" required>
        <option value="">-- Selecciona una categoría --</option>
        <?php
        foreach ($categorias as $cat) {
            echo '<option value="'. $cat['id_categoria'] .'">'. htmlspecialchars($cat['nombre']) .'</option>';
        }
        ?>
    </select><br><br>

    <input type="submit" value="Dar de alta producto">
</form>


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

try{
    // Crear conexion
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Activar modo de errores con excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Obtener categorias para el select
    $stmt = $conn->prepare("SELECT id_categoria from categoria order by id_categoria");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
	  $categorias=$stmt->fetchAll();

    // 2. Procesar el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = limpiar_campos($_POST['nombre']);
        $precio = limpiar_campos($_POST['precio']);
        $id_categoria = limpiar_campos($_POST['id_categoria']);

        // Obtener el máximo id_producto actual para generar el siguiente
        $stmt = $conn->prepare("select max(id_categoria) as max_id from categoria");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultado = $stmt->fetch(); // devuelve solo una fila asociativa

        $num = 1; //si no hay productos empezar por 1
        if ($resultado['max_id'] && $resultado){
          // Quitar la P y convertir a entero
          $num = intval(substr($resultado['max_id'],1)) + 1;
        }

        // Crear nuevo id con formato Pxxxx
        $id_nuevo = "P" . str_pad($num, 4, "0", STR_PAD_LEFT);

        // Insertar el nuevo producto
        $stmt = $conn->prepare("INSERT INTO producto (id_producto, nombre, precio, id_categoria) VALUES (:id, :nombre, :precio, :id_cat)");
        $stmt->bindParam(':id', $id_nuevo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':id_cat', $id_categoria);
        $stmt->execute();

        echo "<p>Producto <strong>$nombre</strong> creado con ID <strong>$id_nuevo</strong></p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;

?>


