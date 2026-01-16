<!-- 5) Consulta de Stock (comconstock.php): se mostrarán los productos en un desplegable y se
mostrará la cantidad disponible del producto seleccionado en cada uno de los almacenes.-->

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

// obtener lista de productos para el desplegable´
function obtener_producto($conn){
    $stmt = $conn->prepare(
        "SELECT id_producto, nombre FROM producto 
         ORDER BY nombre");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener stock de un producto en cada almacen 
function obtener_stock ($conn, $id_producto){
    $stmt = $conn->prepare(
        "SELECT a.num_almacen, a.localidad, al.cantidad 
         FROM almacena al
         JOIN almacen a ON al.num_almacen = a.num_almacen
         WHERE al.id_producto = :id");

    $stmt->bindParam(':id', $id_producto);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$productos = [];
$stock = [];
$mensaje = "";
$id_producto = "";


try {
    $conn      = conectarBD($servername, $username, $password, $dbname);
    $productos = obtener_producto($conn);

    // Procesar formulario (consulta de stock)
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id_producto = limpiar_campos($_POST['id_producto'] ?? '');

        if ($id_producto !== '') {
            $stock = obtener_stock($conn, $id_producto);
            if (empty($stock)) {
                $mensaje = "No hay datos de stock para este producto.";
            }
        } else {
            $mensaje = "Debes seleccionar un producto.";
        }
    }

} catch (PDOException $e) {
    $mensaje = "Error: " . $e->getMessage();
}

?>

<h1>Consulta de Stock por Producto</h1>
<?= $mensaje ?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="id_producto">Producto:</label>
    <select name="id_producto" id="id_producto" required>
       <option value="">Selecciona un producto</option>

       <?php foreach ($productos as $p): ?>
            <option value="<?=  htmlspecialchars($p['id_producto']) ?>"
                <?php if($id_producto == $p['id_producto']) echo 'selected'; ?> >
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
    </select>
    <button type="submit">Consultar stock</button>
</form>

<?php if ($id_producto !== '' && !empty($stock)): ?>
    <h2>Stock del producto <?= htmlspecialchars($id_producto) ?> por almacén</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Almacén</th>
            <th>Almacén</th>
            <th>Cantidad</th>
        </tr>
        <?php foreach ($stock as $fila): ?>
            <tr>
                <td><?= htmlspecialchars($fila['num_almacen']) ?></td>
                <td><?= htmlspecialchars($fila['localidad']) ?></td>
                <td><?= htmlspecialchars($fila['cantidad']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
