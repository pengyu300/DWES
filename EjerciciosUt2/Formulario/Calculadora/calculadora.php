<?php
//Funcion para "limpiar campos" introducidos por los usuarios
function limpiar_campo($campoformulario) {
  $campoformulario = trim($campoformulario); //elimina espacios en blanco por izquierda/derecha
  $campoformulario = stripslashes($campoformulario); //elimina la barra de escape "\", utilizada para escapar caracteres
  $campoformulario = htmlspecialchars($campoformulario);  //convierte caracteres especiales a entidades HTML
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

function dividir($a, $b){
    if ($b == 0){
        return "No se puede dividir entre 0";
    }
    return $a / $b;
}

// Comprobamos que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $num1 = limpiar_campo($_POST["num1"]);
    $num2 = limpiar_campo($_POST["num2"]);
    $operacion = limpiar_campo($_POST["operacion"]);

    if ($operacion == "sumar"){
        $resultado = sumar($num1, $num2);
        $simbolo = "+";
    } elseif ($operacion == "restar"){
        $resultado = restar($num1, $num2);
        $simbolo = "-";
    } elseif ($operacion == "multiplicar"){
        $resultado = multiplicar($num1, $num2);
        $simbolo = "*";
    } elseif ($operacion == "dividir"){
        $resultado = dividir($num1, $num2);
        $simbolo = "/";
    }

    echo "<h1>CALCULADORA</h1>";
    echo "<br>";
    if (is_numeric($resultado)) {
        echo "Resultado operación: $num1 $simbolo $num2 = $resultado";
    } else {
        echo "Resultado operación: $resultado"; // mensaje si hay error
    }
    echo "<br><br><a href='calculadora.html'>Volver</a>";
}

?>
