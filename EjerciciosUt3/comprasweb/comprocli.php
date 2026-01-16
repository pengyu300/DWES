<?php
session_start();

// Comprobar si está logueado
if (!isset($_SESSION['nif'])){
    header("Location: comlogincli.php");
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "rootroot"; 
$dbname     = "COMPRASWEB";


function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

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

// Función para obtener id, nombre y el precio del producto
function obtenerProductos($conn){
    $stmt = $conn->prepare(
        "SELECT id_producto, nombre, precio
        from producto");

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
}

function buscarAlmacen($conn, $id_producto){
    $stmt = $conn->prepare(
        "SELECT sum(cantidad) as stock
         from almacena
         where id_producto = :id_producto"
    );

    $stmt->bindParam(':id_producto', $id_producto);
    $stmt->execute();

    // devuelve un array asociativo
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    // sum(cantidad) devulve null si no hay filas
    if ($fila && $fila['stock'] !== null){
        return (int)$fila['stock'];
    } else{
        return 0;
    }
}


// Función para cargar el carrito desde cookie si existe
function cargarCarrito(){
    if (isset($_COOKIE['carrito'])){ // Comprobar si eciste una cookie llamada carrito
        // convierte string en un array
        $dato = @unserialize($_COOKIE['carrito']);
        if (is_array($dato)){
            return $dato;
        }
    }
    return [];
}

// Función para gardar carrito en cookie
// array -> string
function guardarCarrito($carrito){
    // Convertir el array del carrito en un string para gurdar en una cookie
    $cadena = serialize($carrito);
    // Cookie
    setcookie("carrito", $cadena, time() + 86400 * 30, "/");
}

// Eliminar la cookie del carrito
function vaciarCarrito(){
    setcookie("carrito", "", time() - 3600, "/");
}


// Insertar una linea de compra
function insertarCompra($conn, $nif, $id_producto, $unidades){
    $fecha = date('Y-m-d'); // obtener la fecha actual

    $stmt = $conn->prepare(
        "INSERT INTO compra (nif, id_producto, fecha_compra, unidades)
         VALUES (:nif, :id_producto, :fecha_compra, :unidades)"
    );

    $stmt->bindParam(':nif', $nif);
    $stmt->bindParam(':id_producto', $id_producto);
    $stmt->bindParam(':fecha_compra', $fecha);
    $stmt->bindParam(':unidades', $unidades);

    $stmt->execute();
}


$mensaje = "";
$nif = $_SESSION['nif'];
$usuario = $_SESSION['usuario'];

$conn = conectarBD($servername, $username, $password, $dbname);
$productos = obtenerProductos($conn);
$carrito = cargarCarrito();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // según el boton anadir o finalizar, si no existe accion, usa cadena vacía
    $accion = $_POST['accion'] ?? '';

    if ($accion == "anadir"){
        $id_producto = limpiar_campos($_POST['id_producto']);
        $unidades =(int)limpiar_campos($_POST['unidades']);

        if ($unidades <= 0) {
            $mensaje = "Las unidades deben ser mayores que 0";
        }else {
            // Buscar nombre del producto
            $nombre_producto = "";
            foreach ($productos as $p){
                if($p['id_producto'] == $id_producto){
                    $nombre_producto = $p['nombre'];
                    break;
                }
            }

            if($nombre_producto == "") {
                $mensaje = "Producto no valido";
            } else{
                // Actulizar carrito usando array asociativo
                if (isset($carrito[$id_producto])){
                    $carrito[$id_producto]['unidades'] += $unidades;
                } else{
                    $carrito[$id_producto] = [
                        'nombre' => $nombre_producto,
                        'unidades' => $unidades
                    ];
                }

                // Gurdar en la cookie
                guardarCarrito($carrito);
                $mensaje = "Producrto añadido al carrito";
            }

        }
    }

    // finalizar compra
    elseif ($accion == "finalizar"){
        if (empty($carrito)){
            $mensaje = "El carrito está vacío";
        } else{
            $ok = true;
            $errores = "";

            // Comprobar stock de todos los productos
            foreach($carrito as $id_producto =>$item) {
                $stock = buscarAlmacen($conn, $id_producto);
                if ($stock < $item['unidades']){
                    $ok = false;
                    $errores = "No hay stock suficiente del producto";
                }
            }

            if(!$ok){
                $mensaje = $errores;
            } else{
                //Registrar compras en la tabla
                try{
                    $conn->beginTransaction();

                    foreach($carrito as $id_producto=>$item) {
                        insertarCompra($conn, $nif, $id_producto, $item['unidades']);
                    }

                    $conn->commit();
                    //Vaciar carrito (cookie)
                    vaciarCarrito();
                    $carrito = [];
                    $mensaje = "Compra realizado corrcetamente";
                } catch (PDOException $e){
                    $conn->rollBack();
                    $mensaje = "Error al registrar la compra" . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra de productos</title>
</head>
<body>
    <h1>Compra de productos</h1>
    <p>Cliente: <strong><?php echo htmlspecialchars($usuario); ?></strong></p>

    <?php
    if ($mensaje != ""){
        echo "<p>$mensaje</p>";
    }
    ?>


    <h2>Añadir producto al carrito</h2>
    <form action="comprocli.php" method="POST">
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
    <p>Unidades: <input type="number" name="unidades" value="1" min="1"></p>
    <p>
        <button type="submit" name="accion" value="anadir">Añadir al carrito</button>
    </p>
</form>

<hr>
<h2>Carrito de la compra</h2>
<?php
if (!empty($carrito)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Producto</th><th>Unidades</th></tr>";
    foreach ($carrito as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['nombre']) . "</td>";
        echo "<td>" . (int)$item['unidades'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
?>
    <form action="comprocli.php" method="POST">
        <p>
            <button type="submit" name="accion" value="finalizar">
                Finalizar compra
            </button>
        </p>
    </form>
<?php
} else {
    echo "<p>El carrito está vacío.</p>";
}
?>

<p><a href="comlogincli.php">Volver al menú</a></p>
</body>
</html>







