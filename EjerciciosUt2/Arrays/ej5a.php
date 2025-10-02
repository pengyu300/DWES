<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Array - ej5a</title>
</head>
<body>
    <h4>Programa ej4a.php: Unir arrays</h4>

    <?php
    // 1. Definir los 3 arrays
    $modulos1 = array("Bases Datos", "Entornos Desarrollo", "Programación");
    $modulos2 = array("Sistemas Informáticos", "FOL", "Mecanizado");
    $modulos3 = array("Desarrollo Web ES", "Desarrollo Web EC", "Despliegue", "Desarrollo Interfaces", "Inglés");

    // a) Unir los arrays sin funciones de arrays
    // Crear un array para guardar los módulos
    $unir = array();

    // Recorrer cada array y agregar sus elementos
    for ($i = 0; $i < count($modulos1); $i++) {
        $unir[] = $modulos1[$i];
    }
    for ($i = 0; $i < count($modulos2); $i++) {
        $unir[] = $modulos2[$i];
    }
    for ($i = 0; $i < count($modulos3); $i++) {
        $unir[] = $modulos3[$i];
    }

    echo "<h4>a) Unión manual sin funciones de arrays:</h4>";
    echo implode(", ", $unir); //convierte un array en una cadena de texto uniendo con ,

    // b) Unir los arrays usando array_merge()
    $union_merge = array_merge($modulos1, $modulos2, $modulos3);
    echo "<h4>b) Unión usando array_merge():</h4>";
    echo implode(", ", $union_merge);

    // c) Unir los arrays usando array_push()
    $union_push = $modulos1; // copiamos el primer array
    array_push($union_push, ...$modulos2, ...$modulos3); // agregamos los otros arrays con el operador splat (...)
    echo "<h4>c) Unión usando array_push():</h4>";
    echo implode(", ", $union_push);
    ?>


</body>
</html>
