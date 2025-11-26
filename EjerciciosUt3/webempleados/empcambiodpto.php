<!--Realizar un programa en php empcambiodpto.php que permita seleccionar el DNI de un empleado de una lista desplegable y 
permita asignarlo a un nuevo departamento. Este nuevo departamento se obtendrá también de un desplegable.-->

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

// Función para obtener todos los empleados (dni, nombre, apellidos)
function obtenerEmpleados($conn) {
    $stmt = $conn->prepare(
        "SELECT dni, nombre, apellidos 
         FROM empleado 
         ORDER BY nombre"
    );
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}


// Función para obtener departamentos
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


// Función para actualizar departamento de un empleado
function actualizarDepartamentoEmpleado($conn, $dni, $nuevo_dpto) {
    $stmt = $conn->prepare("UPDATE empleado SET cod_dpto = :cod_dpto WHERE dni = :dni");
    $stmt->bindParam(':cod_dpto', $nuevo_dpto);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
}

try{
    // Conexión
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener lista de empleados
    $empleados = obtenerEmpleados($conn);

    // Departamentos
    $dptos = obtenerDepartamentos($conn);
    

    // Procesar formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $dni = limpiar_campos($_POST['dni']);
        $nuevo_dpto = limpiar_campos($_POST['nuevo_dpto']);

        // Iniciar transacción
        $conn->beginTransaction();

        // Actualizar departamento del empleado
        actualizarDepartamentoEmpleado($conn, $dni, $nuevo_dpto);

        // Confirmar
        $conn->commit();

        echo "<p>Empleado con DNI <strong>" . htmlspecialchars($dni) . "</strong> cambiado al departamento <strong>" . htmlspecialchars($nuevo_dpto) . "</strong>.</p>";
    }

}catch (PDOException $e) {
    if ($conn && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
    echo "<br>Código de error: " . $e->getCode();
}

$conn = null;

?>


<h2>Cambiar Departamento de Empleado</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <label for="dni">Empleado (DNI):</label>
    <select name="dni" id="dni" required>
        <option value="">-- Seleccionar empleado --</option>
        <?php
        foreach ($empleados as $emp) {
            echo '<option value="'.htmlspecialchars($emp['dni']).'">'
                .htmlspecialchars($emp['dni']).' - '.htmlspecialchars($emp['nombre']).' '.htmlspecialchars($emp['apellidos'])
                .'</option>';
        }
        ?>
    </select><br><br>

    <label for="nuevo_dpto">Nuevo Departamento:</label>
    <select name="nuevo_dpto" id="nuevo_dpto" required>
        <option value="">-- Seleccionar departamento --</option>
        <?php
        foreach ($dptos as $dpto) {
            echo '<option value="'.htmlspecialchars($dpto['cod_dpto']).'">'.htmlspecialchars($dpto['nombre_dpto']).'</option>';
        }
        ?>
    </select><br><br>

    <input type="submit" value="Cambiar Departamento">
</form>
