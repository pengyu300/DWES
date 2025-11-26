<!-- Realizar un programa en php emplistadpto.php que permita seleccionar el nombre de un
departamento y muestre por pantalla los empleados que trabajan actualmente en ese departamento.-->

<?php
$servername = "localhost";
$username = "root";
$password = "rootroot";
$dbname = "empleados";

function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Obtener lista de departamentos
function obtenerDepartamentos($conn) {
    $stmt = $conn->prepare(
        "SELECT cod_dpto, nombre_dpto 
         FROM departamento 
         ORDER BY cod_dpto"
    );
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

// Obtener empleados del departamento elegido
function obtenerEmpleadosPorDepartamento($conn, $cod_dpto) {
    $stmt = $conn->prepare(
        "SELECT dni, nombre, apellidos 
         FROM empleado 
         WHERE cod_dpto = :cod_dpto"
    );
    $stmt->bindParam(':cod_dpto', $cod_dpto);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}


try {
    // Conexión
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $departamentos = obtenerDepartamentos($conn);

    // Procesar formulario
    $empleados = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $cod_dpto = limpiar_campos($_POST['cod_dpto']);

        // Obtener empleados del departamento elegido
        $empleados = obtenerEmpleadosPorDepartamento($conn, $cod_dpto);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();

    // Código de error (SQLSTATE)
    echo "Código de error: " . $e->getCode() . "<br>";
}

$conn = null;
?>

<h2>Listado de Empleados por Departamento</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <label for="cod_dpto">Departamento:</label>
    <select name="cod_dpto" id="cod_dpto" required>
        <option value="">-- Seleccionar departamento --</option>
        <?php
        foreach ($departamentos as $dpto) {
            echo '<option value="'.htmlspecialchars($dpto['cod_dpto']).'">'
                .htmlspecialchars($dpto['nombre_dpto'])
                .'</option>';
        }
        ?>
    </select>

    <br><br>
    <input type="submit" value="Mostrar Empleados">
</form>

<?php
// Mostrar resultados si se ha enviado formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo "<h3>Empleados en el departamento seleccionado:</h3>";

    if (count($empleados) > 0) {
        echo "<ul>";
        foreach ($empleados as $emp) {
            echo "<li>"
                . htmlspecialchars($emp['dni']) . " - "
                . htmlspecialchars($emp['nombre']) . " "
                . htmlspecialchars($emp['apellidos'])
                . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay empleados en este departamento.</p>";
    }
}
?>
