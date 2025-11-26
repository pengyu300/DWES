<!--Programa en php empaltaemp.php que permita dar de alta un empleado en la
empresa. Para seleccionar el departamento, al que se asignará al empleado inicialmente, se
utilizará una lista de valores con los nombres de los departamentos de la empresa.-->

<?php

$servername = "localhost";
$username   = "root";
$password   = "rootroot";
$dbname     = "empleados";

function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
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

// Función para insertar nuevo empleado
function insertarEmpleado($conn, $dni, $nombre, $apellidos, $fecha_nac, $salario, $cod_dpto) {
    $stmt = $conn->prepare(
        "INSERT INTO empleado (dni, nombre, apellidos, fecha_nac, salario, cod_dpto)
         VALUES (:dni, :nombre, :apellidos, :fecha_nac, :salario, :cod_dpto)"
    );
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':fecha_nac', $fecha_nac);
    $stmt->bindParam(':salario', $salario);
    $stmt->bindParam(':cod_dpto', $cod_dpto);
    $stmt->execute();
}


try {
    // Conexión
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener departamentos
    $departamentos = obtenerDepartamentos($conn);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $dni       = limpiar_campos($_POST['dni']);
        $nombre    = limpiar_campos($_POST['nombre']);
        $apellidos = limpiar_campos($_POST['apellidos']);
        $fecha_nac = limpiar_campos($_POST['fecha_nac']);
        $salario   = limpiar_campos($_POST['salario']);
        $cod_dpto  = limpiar_campos($_POST['cod_dpto']);

        // Iniciar transacción
        $conn->beginTransaction();

        // Insertar
        insertarEmpleado($conn, $dni, $nombre, $apellidos, $fecha_nac, $salario, $cod_dpto);

        // Confirmar transacción
        $conn->commit();

        echo "<p>Empleado dado de alta correctamente.</p>";
    }

}catch (PDOException $e) {
    
    echo "Error: " . $e->getMessage();
    echo "Código de error: " . $e->getCode() . "<br>";
}

$conn = null;

?>


<!-- FORMULARIO -->
<h2>Alta de Empleado</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    DNI: <input type="text" name="dni" maxlength="9" required><br><br>
    Nombre: <input type="text" name="nombre" required><br><br>
    Apellidos: <input type="text" name="apellidos" required><br><br>
    Fecha Nacimiento: <input type="date" name="fecha_nac" required><br><br>
    Salario: <input type="number" step="0.01" name="salario" required><br><br>

    Departamento:
    <select name="cod_dpto" required>
        <option value="">Seleccionar departamento</option>
        <?php
        foreach ($departamentos as $dpto) {
            echo '<option value="'.htmlspecialchars($dpto['cod_dpto']).'">'.htmlspecialchars($dpto['nombre_dpto']).'</option>';
        }
        ?>
    </select><br><br>

    <input type="submit" value="Dar de alta empleado">
</form>
