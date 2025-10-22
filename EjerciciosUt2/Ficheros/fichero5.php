<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichero 5</title>
</head>
<body>
    <h2>Operaciones Ficheros</h2>

<?php
    // Inicializar las variables
    $archivo = "";
    $operacion = "";
    $numero = 0;
    $resultado = "";

    // Función para limpiar campos
    function limpiar_campos($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label>Fichero (Path/nombre): </label><br>
    <input type="text" name ="archivo" value="<?php echo $archivo; ?>" required><br><br>

     <label>Selecciona operación:</label><br>
    <input type="radio" name="operacion" value="mostrar_todo" <?php if($operacion=="mostrar_todo") echo "checked"; ?>> Mostrar fichero completo<br>
    <input type="radio" name="operacion" value="mostrar_linea" <?php if($operacion=="mostrar_linea") echo "checked"; ?>> Mostrar línea n<br>
    <input type="radio" name="operacion" value="primeras_lineas" <?php if($operacion=="primeras_lineas") echo "checked"; ?>> Mostrar primeras n líneas<br><br>

    <label>Número (para línea n o primeras n líneas):</label><br>
    <input type="number" name="numero" value="<?php echo $numero; ?>"><br><br>

    <input type="submit" value="Enviar">
    <input type="reset" value="Borrar">
</form>

<?php
    // Si se envió el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $archivo = limpiar_campos($_POST["archivo"] ?? "");
        $operacion = limpiar_campos($_POST["operacion"] ?? "");
        $numero = limpiar_campos(intval($_POST["numero"] ?? 0));

        if(!file_exists($archivo)){
            $resultado = "El fichero no existe";
        }
        else{
            $lineas= file($archivo); // Guardar el archivo en un array
            $total = count($lineas);

            if ($operacion == "mostrar_todo"){
                // Une todas las lineas en un solo string
                $resultado = "<pre>" . implode("", $lineas) . "</pre>";
            }
            elseif ($operacion == "mostrar_linea"){
                if ($numero < 1 || $numero > $total){
                    $resultado = "Linea fuera del rango";
                }
                else{
                    // accede al numero de línea, resta 1 porque los arrays empiezan en 0
                    $resultado = "<pre>" . $lineas[$numero - 1] . "</pre>";
                }
            }
            elseif ($operacion == "primeras_lineas"){
                if ($numero < 1 || $numero > $total){
                    $resultado = "Numero de lineas invalido";
                }
                else{
                    // Corta el array $lineas desde la posicion 0 hasta la posicion $numero-1
                    $resultado = "<pre>" . implode("", array_slice($lineas, 0, $numero)) . "</pre>";
                }
            }
            else{
                $resultado = "Operacion no valida";
            }
        }

    }
?>

<h3>Resultado</h3>
<?php
echo $resultado;
?>



</body>
</html>
