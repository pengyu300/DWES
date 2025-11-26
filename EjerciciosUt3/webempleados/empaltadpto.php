<!-- El código del departamento tendrá el formato DxxxN (‘D001’, ‘D002’ …) y se
obtendrá automáticamente.-->

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

// Función para obtener el último código departamento
function obtenerUltimoCodigoDepartamento($conn) {
    $stmt = $conn->prepare(
        "SELECT cod_dpto FROM departamento
         ORDER BY cod_dpto DESC
         LIMIT 1"
    );
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetch();
}


// Función para insertar nuevo departamento
function insertarDepartamento($conn, $cod_dpto, $nombre) {
    $stmt = $conn->prepare("
        INSERT INTO departamento (cod_dpto, nombre_dpto)
        VALUES (:cod_dpto, :nombre)
    ");
    $stmt->bindParam(':cod_dpto', $cod_dpto);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->execute();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Lanzar excepciones ante errores
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Limpiar nombre
        $nombre = strtoupper(limpiar_campos($_POST["nombre"]));

        // Iniciar transacción
        $conn->beginTransaction();

        // Calcular siguiente código
        $ultimo = obtenerUltimoCodigoDepartamento($conn);

        if ($ultimo) {
            $num = intval(substr($ultimo["cod_dpto"], 1)) + 1;
        }else{
            $num = 1;
        }

        // Crear nuevo id con formato DxxxN
        $cod_dpto = "D" . str_pad($num, 3, "0", STR_PAD_LEFT);

        // Insertar
        insertarDepartamento($conn, $cod_dpto, $nombre);

        // Confirmar transacción
        $conn->commit();

        echo "<p>Departamento creado:</p>";
        echo "<p>Código: <strong>$cod_dpto</strong><br>Nombre: <strong>$nombre</strong></p>";


    } catch (PDOException $e) {

  
    echo "Error: " . $e->getMessage() . "<br>";

    // Código de error (SQLSTATE)
    echo "Código de error: " . $e->getCode() . "<br>";

    
    }


$conn = null;
}

?>
