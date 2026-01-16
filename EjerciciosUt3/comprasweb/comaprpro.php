<!--4) Aprovisionar Productos (comaprpro.php): asignar una cantidad de un determinado producto
a un almacén. Se seleccionarán los nombres de los productos y los números de los almacenes
desde listas desplegables. El usuario introducirá la cantidad del producto a aprovisionar.--> 

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

// Obtener producto
function obtenerProductos($conn){
    $stmt = $conn->prepare(
        "SELECT id_producto, nombre
        from producto 
        order by nombre"
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener numero y localidad del almacen
function obtenerAlmacen($conn){
    $stmt = $conn->prepare(
        "SELECT num_almacen, localidad
         from almacen
         order by num_almacen"
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener cantidad actual en almacena para un prodcuto en un almacen
function obtenerCantidad($conn, $num_almacen, $id_producto){
    $stmt = $conn->prepare(
        "SELECT cantidad from almacena
         where num_almacen = :num_almacen
         and id_producto = :id_producto"
    );

    $stmt->bindParam(':num_almacen', $num_almacen);
    $stmt->bindParam(':id_producto', $id_producto);

    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->execute();

    if ($fila){
        return (int)$fila['cantidad'];
    } else{
        return null; //no existe
    }
}


// insertar una nueva fila en almacena
function insertarAlmacena ($conn, $num_almacen, $id_producto, $cantidad){
    $stmt = $conn->prepare(
        "INSERT INTO almacena (num_almacen, id_producto, cantidad)
         VALUES (:num_almacen, :id_producto, :cantidad)"
    );

    $stmt->bindParam(':num_almacen', $num_almacen);
    $stmt->bindParam(':id_producto', $id_producto);
    $stmt->bindParam(':cantidad', $cantidad);

    return $stmt->execute();
}

// Actualizar cantidad en almacena
function actualizarAlmacena($conn, $num_almacen, $id_producto, $cantidadSumar){
    $stmt = $conn->prepare(
        "UPDATE almacena
         set cantidad = cantidad + :cantidad
         where num_almacen = :num_almacen
         and id_producto = :id_producto"
    );

    $stmt->bindParam(':num_almacen', $num_almacen);
    $stmt->bindParam(':id_producto', $id_producto);
    $stmt->bindParam(':cantidad', $cantidadSumar);

    return $stmt->execute();
}

$mensaje = "";
$conn = conectarBD($servername, $username, $password, $dbname);
$productos = obtenerProductos($conn);
$almacenes = obtenerAlmacen($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto  = limpiar_campos($_POST['id_producto']);
    $num_almacen  = (int)limpiar_campos($_POST['num_almacen']);
    $cantidad    = (int)limpiar_campos($_POST['cantidad']);

    if ($cantidad <= 0){
        $mensaje = "La cantidad debe ser mayor que 0";
    } else {
        // Comprobar si ya existe ese registro
        $cantidadActual = obtenerCantidad($conn, $num_almacen, $id_producto);

        if ($cantidadActual === null){
            // Si no existe, insertar
            if(insertarAlmacena($conn, $num_almacen, $id_producto, $cantidad)){
                $mensaje = "Producto aprovisionado correctamente";
            }else{
                $mensaje = "Error al insertar en almacena";
            }
        } else{
            // Ya existe, actualizar
            if(actualizarAlmacena($conn, $num_almacen, $id_producto, $cantidad)){
                $mensaje = "Cantidad actualizada correctamente";
            } else{
                $mensaje = "Error al actualizar almacena";
            }
        }
    }
}

$conn = null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovisionar productos</title>
</head>
<body>
    <h1>Aprovisionar productos</h1>

    <?php 
    if($mensaje != ""){
        echo "<p>$mensaje</p>";
    } 
    ?>

    <form action="comaprpro.php" method="POST">
    <p>Producto:
        <select name="id_producto">
            <?php
            foreach ($productos as $p) {
                echo "<option value='" . htmlspecialchars($p['id_producto']) . "'>"
                   . htmlspecialchars($p['nombre'])
                   . "</option>";
            }
            ?>
        </select>
    </p>

    <p>Almacén:
        <select name="num_almacen">
            <?php
            foreach ($almacenes as $a) {
                echo "<option value='" . htmlspecialchars($a['num_almacen']) . "'>"
                   . htmlspecialchars($a['num_almacen'] . " - " . $a['localidad'])
                   . "</option>";
            }
            ?>
        </select>
    </p>

    <p>Cantidad a aprovisionar:
        <input type="number" name="cantidad" min="1" value="1" required>
    </p>

    <p><input type="submit" value="Aprovisionar"></p>
</form>
</body>
</html>
