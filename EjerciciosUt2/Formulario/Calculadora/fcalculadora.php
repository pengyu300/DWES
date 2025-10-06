<?php
// Funciones
function limpiar_campo($campoformulario) {
  $campoformulario = trim($campoformulario);
  $campoformulario = stripslashes($campoformulario);
  $campoformulario = htmlspecialchars($campoformulario);
  return $campoformulario;
}

function sumar($a, $b){ 
    return $a + $b;
}

function restar($a, $b){ 
    return $a - $b; 
}

function multiplicar($a, $b){ 
    return $a * $b; 
}

function dividir($a, $b)
{
    if ($b == 0){
        return "No se puede dividir entre 0";
    }
    return $a / $b;
}

$resultado = "";
$simbolo = "";
$num1 = "";
$num2 = "";
$operacion = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num1 = limpiar_campo($_POST["num1"]);
    $num2 = limpiar_campo($_POST["num2"]);
    $operacion = limpiar_campo($_POST["operacion"]);

    if ($operacion == "sumar") {
        $resultado = sumar($num1, $num2);
        $simbolo = "+";
    } elseif ($operacion == "restar") {
        $resultado = restar($num1, $num2);
        $simbolo = "-";
    } elseif ($operacion == "multiplicar") {
        $resultado = multiplicar($num1, $num2);
        $simbolo = "*";
    } elseif ($operacion == "dividir") {
        $resultado = dividir($num1, $num2);
        $simbolo = "/";
    }

    // Vaciar campos después de calcular
    $num1 = "";
    $num2 = "";
    $operacion = "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora PHP</title>
</head>
<body>
    <h1>CALCULADORA</h1>

    <!-- Formulario -->
    <!-- Hace que el formulario se envíe a la misma página -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
    Operando 1: 
    <input type="number" name="num1" step="any" value="<?php echo $num1; ?>">
    <br><br>

    Operando 2: 
    <input type="number" name="num2" step="any" value="<?php echo $num2; ?>">
    <br><br>

    Selecciona operación:<br>
    <input type="radio" name="operacion" value="sumar"> Suma<br>
    <input type="radio" name="operacion" value="restar"> Resta<br>
    <input type="radio" name="operacion" value="multiplicar"> Multiplicación<br>
    <input type="radio" name="operacion" value="dividir"> División<br><br>


    <input type="submit" value="Calcular">
    </form>

    <br>

    <!-- Mostrar resultado solo si se envió el formulario -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (is_numeric($resultado)) {
            echo "<p>Resultado operación: " . $_POST["num1"] . " $simbolo " . $_POST["num2"] . " = $resultado</p>";
        } else {
            echo "<p>Resultado operación: $resultado</p>";
        }
    }
    ?>

</body>
</html>
