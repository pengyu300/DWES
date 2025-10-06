<html>
    <head>
        <title>Cambio Base</title>
    </head>
    <body>
        <h1>CONVERSOR NUMERICO</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

            <label for="decimal">Numero decimal:</label>
            <input type="text" name="decimal" id="decimal" required>
            <br><br>

            Convertir a <br>
            <input type="radio" name="operacion" value="binario"> Binario<br>
            <input type="radio" name="operacion" value="octal"> Octal<br>
            <input type="radio" name="operacion" value="hexadecimal"> Hexadecimal<br>
            <input type="radio" name="operacion" value="todos"> Todos sistemas<br><br>

            <input type="submit" value="Enviar">
            <input type="reset" value="Borrar">
        </form>
    </body>
</html>

<?php
// Función para limpiar campos
function limpiar_campos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Funciones
function binario($numero){
    return decbin($numero);
}

function octal($numero){
    return decoct($numero);
}

function hexadecimal($numero){
    return dechex($numero);
}

// Función para mostrar una sola tabla con todos sistemas
function mostrarTablaTodos($numero) {
    echo "<h3>Número decimal: $numero</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='2'>";
    echo "<tr><td>Binario</td><td>" . binario($numero) . "</td></tr>";
    echo "<tr><td>Octal</td><td>" . octal($numero) . "</td></tr>";
    echo "<tr><td>Hexadecimal</td><td>" . hexadecimal($numero) . "</td></tr>";
    echo "</table><br>";
}

// Función para mostrar tabla individual
function mostrarTabla($sistema, $resultado, $numero){
    echo "<table border='1' cellpadding='5' cellspacing='2'>";
    echo "<tr><th>Decimal</th><th>$sistema</th></tr>";
    echo "<tr><td>$numero</td><td>$resultado</td></tr>";
    echo "</table><br>";
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decimal = limpiar_campos($_POST['decimal']);
    $operacion = limpiar_campos($_POST['operacion']);

    if (!is_numeric($decimal) || intval($decimal) < 0) {
        echo "<h3>Introduce un número decimal válido.</h3>";
        exit;
    }

    // Convierte $decimal a un numero entero
    $decimal = intval($decimal);

    switch ($operacion) {
        case 'binario':
            mostrarTabla('Binario', binario($decimal), $decimal);
            break;
        case 'octal':
            mostrarTabla('Octal', octal($decimal), $decimal);
            break;
        case 'hexadecimal':
            mostrarTabla('Hexadecimal', hexadecimal($decimal), $decimal);
            break;
        case 'todos':
            mostrarTablaTodos($decimal);
            break;
        default:
            echo "<p>Selecciona una opción válida.</p>";
            break;
    }
}
?>
