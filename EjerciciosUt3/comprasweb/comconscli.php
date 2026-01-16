<!-- 13) comconscli.php: Consulta de compras del cliente, le permitirá al cliente mostrar la
información de los compras realizadas entre dos fechas.--> 

<?php
session_start();

// Comprobar que el cliente está logueado
if (!isset($_SESSION['nif'])) {
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

// Obtener compras del cliente entre dos fechas
function obtenerCompras($conn, $nif, $desde, $hasta){
    $stmt = $conn->prepare(
        "SELECT c.fecha_compra, c.unidades, p.nombre, p.precio
         from compra c
         join producto p on c.id_producto = p.id_producto
         where c.nif = :nif
         and c.fecha_compra between :desde and :hasta
         order by c.fecha_compra"
    );

    $stmt->bindParam(':nif', $nif);
    $stmt->bindParam(':desde', $desde);
    $stmt->bindParam(':hasta', $hasta);
    
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$mensaje = "";
$compras = [];
$total = 0;

$nif = $_SESSION['nif'];
$nombre = $_SESSION['nombre'];

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $desde = limpiar_campos($_POST['desde']);
    $hasta = limpiar_campos($_POST['hasta']);

    if ($desde == "" || $hasta == ""){
        $mensaje = "Indica las dos fechas";
    }
    else{
        $conn = conectarBD($servername, $username, $password, $dbname);
        $compras = obtenerCompras($conn, $nif, $desde, $hasta);
        $conn = null;

        if (empty($compras)){
            $mensaje = "No hay compras entre estas dos fechas.";
        } else{
            foreach ($compras as $com){
                $total += $com['unidades'] * $com['precio']; 
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
    <title>Consulta de compras</title>
</head>
<body>
    <h1>Consulta de compras</h1>

    <p>Cliente: <strong><?php echo htmlspecialchars($nombre); ?>
    </strong> (NIF: <?php echo htmlspecialchars($nif); ?>)</p>

    <form action ="comconscli.php" method="POST">
        <p>Fecha desde: <input type="date" name="desde" required></p>
        <p>Fecha hasta: <input type="date" name="hasta" required></p>
        <p><input type="submit" value="Consultar compras"></p>
    </form>

    <?php
    if ($mensaje != "") {
        echo "<p>$mensaje</p>";
    }

    if (!empty($compras)) {
        echo "<h2>Resultados</h2>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Unidades</th>
                <th>Precio</th>
                <th>Importe línea</th>
            </tr>";

        foreach ($compras as $c) {
            $importeLinea = $c['unidades'] * $c['precio'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($c['fecha_compra']) . "</td>";
            echo "<td>" . htmlspecialchars($c['nombre'])       . "</td>";
            echo "<td>" . (int)$c['unidades']                   . "</td>";
            echo "<td>" . number_format($c['precio'], 2)        . "</td>";
            echo "<td>" . number_format($importeLinea, 2)       . "</td>";
            echo "</tr>";
    }
    echo "</table>";

    echo "<p><strong>Total compras: " . number_format($total, 2) . " €</strong></p>";
}
?>
 
<p><a href="comlogincli.php">Volver al menú</a></p>

</body>
</html>
