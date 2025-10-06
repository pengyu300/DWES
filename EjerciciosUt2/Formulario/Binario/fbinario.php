<?php

function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Iniciar la variable que va a contener el resultado
$binario = "";

// Verificar que el formulario ha sido enviado
if (($_SERVER["REQUEST_METHOD"] == "POST") ){
    // obtener el núemro decimal del formulario y limpiar
    $decimal = limpiar_campos($_POST['decimal']);

    // validar que sea un numero entero
    if (is_numeric($decimal) && intval($decimal) == $decimal){
        // convertir a binario
        $binario = decbin($decimal);
    }
    else{
        $binario = "Debe ser un número entero";
    }
}

?>

<html>
<head>
    <title>Binario PHP</title>
</head>
<body>
    <h1>CONVERSOR BINARIO</h1>

    <!-- Hace que el formulario se envíe a la misma página -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="decimal">Número decimal:</label>
        <input type="text" name="decimal" id="decimal" required>

        <input type="submit" name="submit" value="Convertir"> 
    </form>

    <!-- Mostrar el reaultado -->
    <?php
    echo "<h3>Resultado:</h3>";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "<p>Decimal: $decimal</p>";
        echo "<p>Binario: $binario</p>";
    }
    ?>

</body>
</html>


