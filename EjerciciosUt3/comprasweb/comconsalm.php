<!-- 6) Consulta de Almacenes (comconsalm.php): se mostrarán los almacenes en un desplegable
y se mostrará la información de los productos disponibles en el almacén seleccionado. --> 

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

// Obtener lista de almacenes para el desplegable
function obtener_almacenes($conn){
    $stmt = $conn->prepare(
        "SELECT num_almacen, localidad FROM almacen
         ORDER BY localidad");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener los productos que hay en un almacena específico
function obtener_productos($conn, $num_almacen){
    // almacena: cantidad, id_producto
    // producto: nombre, precio
    $stmt = $conn->prepare(
        "SELECT p.nombre, p.precio, a.cantidad
         FROM almacena a
         join producto p on a.id_producto = p.id_producto
         where a.num_almacen = :num_almacen");

    $stmt->bindParam(':num_almacen', $num_almacen);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$almacenes = [];
$productos = [];
$almacen_seleccionado ="";
$mensaje = "";

try{
    $conn = conectarBD($servername, $username, $password, $dbname);
    $almacenes = obtener_almacenes($conn);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $almacen_seleccionado = limpiar_campos($_POST['num_almacen'] ?? '');

        if (!empty($almacen_seleccionado)){
            // Buscar en la tabla almacena
            $productos = obtener_productos($conn, $almacen_seleccionado);

            if (empty($productos)){
                $mensaje = "<p>No hay productos disponibles en este almacén</p>";

            } 
        }
    }

} catch (PDOException $e) {
    $mensaje = "Error: " . $e->getMessage();
}

?>


<h1>Consulta de Productos por Almacén</h1>
<?= $mensaje ?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="num_almacen">Almacén:</label>
    <select name="num_almacen" id="num_almacen" required>
        <option value="">Selecciona un almacén</option>
        <?php foreach ($almacenes as $alm): ?>
            <option value="<?= $alm['num_almacen'] ?>"
                    <?php if($almacen_seleccionado == $alm['num_almacen']) echo 'selected'; ?>>
                    <?= htmlspecialchars($alm['localidad']) ?>
            </option>

        <?php endforeach; ?>
    </select>

    <button type="submit">Consultar</button>
</form>

<?php if (!empty($productos)): ?>
    <h2>Productos disponibles en el almacén <?= htmlspecialchars($almacen_seleccionado) ?></h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Cantidad</th>
        </tr>
        <?php foreach ($productos as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['nombre']) ?></td>
                <td><?= htmlspecialchars($prod['precio']) ?></td>
                <td><?= htmlspecialchars($prod['cantidad']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

