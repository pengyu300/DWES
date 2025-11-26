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

try{
    // Conexión
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener lista de empleados para el select (dni y nombre)
    $stmt = $conn->prepare(
        "select dni, nombre, apellidos from empleado 
         order by nombre"
    );
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $empleados = $stmt->fetchAll();

    // Departamentos
    $stmt = $conn->prepare(
        "select cod_dpto, nombre_dpto from departamento 
         order by cod_dpto"
    );
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $dptos = $stmt->fetchAll();

    // Procesar formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $dni = limpiar_campos($_POST['dni']);
        $nuevo_dpto = limpiar_campos($_POST['nuevo_dpto']);

        // Iniciar transacción
        $conn->beginTransaction();

        // Actualizar departamento del empleado
        $stmt = $conn->prepare("UPDATE empleado SET cod_dpto = :cod_dpto WHERE dni = :dni");
        $stmt->bindParam(':cod_dpto', $nuevo_dpto);
        $stmt->bindParam(':dni', $dni);

        $stmt->execute();

        // Confirmar
        $conn->commit();

        echo "<p>Empleado con DNI <strong>" . htmlspecialchars($dni) . "</strong> cambiado al departamento <strong>" . htmlspecialchars($nuevo_dpto) . "</strong>.</p>";
    }

}catch (PDOException $e) {
    if ($conn && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
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
