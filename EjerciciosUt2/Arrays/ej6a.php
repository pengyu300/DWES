<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Array - ej6a</title>
</head>
<body>
    <h4>Programa ej4a.php: Mostrar el array en orden inverso</h4>

    <?php
    // 1. Definir los 3 arrays
    $modulos1 = array("Bases Datos", "Entornos Desarrollo", "Programación");
    $modulos2 = array("Sistemas Informáticos", "FOL", "Mecanizado");
    $modulos3 = array("Desarrollo Web ES", "Desarrollo Web EC", "Despliegue", "Desarrollo Interfaces", "Inglés");

    // Unir los arrays
    $unir = array_merge($modulos1,$modulos2,$modulos3);

    // 2. Eliminar el módulo mecanizado
    $eliminar = array_search("Mecanizado",$unir); //busca mecanizado
    if($eliminar !== false){
        unset($unir[$eliminar]); //lo elimina
    }

    // 3. En orden inverso
    $unir_inverso = array_reverse($unir);

    // 4. Mostrar en orden inverso
    for ($i = 0; $i < count($unir_inverso); $i++){
    echo $unir_inverso[$i] . "<br>";
    }

    ?>


</body>
</html>
