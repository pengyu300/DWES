<?php
// Inicio de la sesión
session_start();

// Cerrar sesión
if (isset($_GET['logout'])) {
    // Borrar variables de sesión
    session_unset();
    // Destruir la sesión
    session_destroy();

    // borrar la cookie del carrito
    setcookie("carrito", "", time() - 3600, "/");

    // Redirigir al login
    header("Location: comlogincli.php");
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "rootroot"; 
$dbname     = "COMPRASWEB";

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

function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Función para comprobar el usuario y la clave en la BD
function comprobar_login($conn, $usuario, $clave){
    try{
        $stmt = $conn->prepare(
        "SELECT NIF, NOMBRE from cliente
         where USUARIO = :usuario and CLAVE = :clave"
        );

        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();

        // 如果能查到行，说明账号密码匹配
        if ($stmt->rowCount() > 0) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            return $stmt->fetch(); // 返回一行数据 (Array)
        } else {
            return false; // 登录失败返回 false
        }
    
    } catch (PDOException $e) {
        return false;
    }
}

$mensaje = "";
$usuarioSesion = null;

// Si ya hay sesión iniciada
if (isset($_SESSION['usuario'])) {
    $usuarioSesion = $_SESSION['usuario'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $usuarioForm = limpiar_campos($_POST['usuario']);
    $claveForm   = limpiar_campos($_POST['clave']);

    $conn = conectarBD($servername, $username, $password, $dbname);

    $datosCliente = comprobar_login($conn, $usuarioForm, $claveForm);

    if ($datosCliente){
        // Login correcto: guardar datos en sesión
        $_SESSION['usuario'] = $usuarioForm;
        $_SESSION['nif'] = $datosCliente['NIF'];
        $_SESSION['nombre']  = $datosCliente['NOMBRE'];

        // Guardar cookie con el usuario
        setcookie("usuario", $usuarioForm, time() + (86400 * 30), "/");
    
        $usuarioSesion = $usuarioForm;
    } else{
        $mensaje = "Usuario o clave incorrecto";
    }
}

$conn = null;

?>

<h1>Login de clientes</h1>

<?php
if ($usuarioSesion) {
    // Ya está logueado
    echo "<p>Has iniciado sesión como: <strong>" . htmlspecialchars($usuarioSesion) . "</strong></p>";
    echo "<p><a href='comlogincli.php?logout=1'>Cerrar sesión</a></p>";
    echo "<hr>";
    echo "<h2>Opciones del portal</h2>";
    echo "<ul>";
    echo "<li><a href='comprocli.php'>Comprar productos</a></li>";
    echo "<li><a href='comconscli.php'>Consultar compras</a></li>";
    echo "</ul>";
} else {
    // Mostrar formulario de login
    if ($mensaje != "") {
        echo "<p style='color:red;'>$mensaje</p>";
    }
?>
    <form action="comlogincli.php" method="POST">
        <p>Usuario: <input type="text" name="usuario" required></p>
        <p>Clave: <input type="password" name="clave" required></p>
        <p><input type="submit" value="Entrar"></p>
    </form>

    <p>¿Aún no estás registrado? <a href="comregcli.php">Regístrate aquí</a></p>
<?php
}
?>
