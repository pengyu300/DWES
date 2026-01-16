<!-- Registro de clientes (comregcli.php) Al darse de alta, se les proporcionará como nombre de 
usuario su nombre y como clave el apellido escrito de manera inversa. 
Realizar en la base de datos las modificaciones que se estimen oportunas. --> 

<?php
function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

$servername = "localhost";
$username   = "root";
$password   = "rootroot";
$dbname     = "COMPRASWEB";

// Función para conectar a la Base de Datos
function conectarDB($servername, $username, $password, $dbname) {
    try {
        // La setencia de conexión
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Configurar atributos de PDO para manejar errores
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;

    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }
}

// normalizar
function normalizar_nif($nif){
    $nif = strtoupper($nif);
    // quitar espacios y guiones
    $nif = str_replace([' ', '-'], '', $nif);

    return $nif;
}

// Función para validar el formato de NIF
function validar_nif($nif){
    // 8 números y 1 letra
    return preg_match('/^[0-9]{8}[A-Za-z]$/', $nif);
}

function generar_clave($apellido){
    return strrev(strtolower($apellido)); // escrito de manera inversa y minúscula
}

// Comprobar si ya existe un cliente con ese nif
function existe_nif($conn, $nif){
    $stmt = $conn->prepare(
        "SELECT nif 
         from cliente 
         where nif = :nif"
    );

    $stmt->bindParam(':nif', $nif);
    $stmt->execute();
    // Si devuelve alguna fila, es que existe
    return $stmt->rowCount() > 0;
}


// Función para insertar el cliente
function insertar_cliente($conn, $nif, $nombre, $apellido, $cp, $direccion, $ciudad, $usuario, $clave){
    $stmt = $conn->prepare(
        "INSERT INTO cliente (nif, nombre, apellido, cp, direccion, ciudad, usuario, clave)
         VALUES (:nif, :nombre, :apellido, :cp, :direccion, :ciudad, :usuario, :clave)"
    );

    $stmt->bindParam(':nif', $nif);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':cp', $cp);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':ciudad', $ciudad);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':clave', $clave);
    
    $stmt->execute();
         
}

$mensaje = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Limpiar datos
    $nif       = limpiar_campos($_POST['nif']);
    $nombre    = limpiar_campos($_POST['nombre']);
    $apellido  = limpiar_campos($_POST['apellido']);
    $cp        = limpiar_campos($_POST['cp']);
    $direccion = limpiar_campos($_POST['direccion']);
    $ciudad    = limpiar_campos($_POST['ciudad']);

    // 2. Generar Usuario y Clave
    $usuario = strtolower($nombre);
    $clave   = strrev(strtolower($apellido)); // Apellido al revés

    // 3. Conexión y Operaciones
    $conn = conectarDB($servername, $username, $password, $dbname);

    try {
        // Validar formato NIF
        if (!validar_nif($nif)) {
            $mensaje = "Error: Formato de NIF incorrecto. Debe ser 8 números + 1 letra.";
        } 
        // Validar si ya existe en BD
        elseif (existe_nif($conn, $nif)) {
            $mensaje = "Error: Ya existe un cliente con ese NIF.";
        } 
        else {
            // Insertar
            insertar_cliente($conn, $nif, $nombre, $apellido, $cp, $direccion, $ciudad, $usuario, $clave);
            
            $mensaje = "<p>Cliente registrado correctamente.</p>
                        <p>Tus credenciales son:<br>
                        Usuario: <b>$usuario</b><br>
                        Clave: <b>$clave</b></p>";
        }

    } catch (PDOException $e) {
        $mensaje = "Error en la base de datos: " . $e->getMessage();
        echo "Código de error: " . $e->getCode() . "<br>";
    }
    
    // Cerrar conexión
    $conn = null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registro de clientes</title>
</head>
<body>
<h1>Registro de clientes</h1>

<?php
if ($mensaje != "") {
    echo "<p>$mensaje</p>";
}
?>

<form action="comregcli.php" method="POST">
    <p>NIF: <input type="text" name="nif" required></p>
    <p>Nombre: <input type="text" name="nombre" required></p>
    <p>Apellido: <input type="text" name="apellido" required></p>
    <p>Código Postal: <input type="text" name="cp"></p>
    <p>Dirección: <input type="text" name="direccion"></p>
    <p>Ciudad: <input type="text" name="ciudad"></p>

    <p><input type="submit" value="Registrar cliente"></p>
</form>

<p><a href="comlogincli.php">Ir a Login</a></p>
</body>
</html>

