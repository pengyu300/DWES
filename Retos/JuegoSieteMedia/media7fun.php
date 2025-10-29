<?php
//Funcion para "limpiar campos" introducidos por los usuarios
function limpiar_campo($campoformulario) {
  $campoformulario = trim($campoformulario);
  $campoformulario = stripslashes($campoformulario);
  $campoformulario = htmlspecialchars($campoformulario);
  return $campoformulario;
}

// Crear las 40 cartas mezcladas
function cartas(){
    $cartas = array('1C','2D','1P','1T',
                    '2C','2D','2P','2T',
                    '3C','3D','3P','3T',
                    '4C','4D','4P','4T',
                    '5C','5D','5P','5T',
                    '6C','6D','6P','6T',
                    '7C','7D','7P','7T',
                    'JC','JD','JP','JT',
                    'QC','QD','QP','QT',
                    'KC','KD','KP','KT',);

    shuffle($cartas); // mezclar
    return $cartas;
}


// Repartir cartas
function repartir ($cartas, $numcartas){
    $cartas_repartidas = array_slice($cartas, 0, $numcartas); // coge desde el índice 0 hasta $numcartas elementos
    $baraja_restante = array_slice($cartas, $numcartas);      // el resto
    return [$cartas_repartidas, $baraja_restante];

}


// Repartir un número de cartas ($numcartas) a cada jugador del array $nombres, usando la baraja mezclada que devuelve la función cartas().
usando la baraja mezclada que devuelve la función cartas().
function repartirJugadores ($nombres, $numcartas){
    $baraja = cartas(); // genera la baraja mezclada
    $jugadores = [];

    // Recorre la lista de jugadores que el usuario introdujo en el formulario.
    // $mano = $cartas_repartidas; $baraja = $baraja_restante;
    foreach ($nombres as $nombre){
        list($mano, $baraja) = repartir($baraja, $numcartas);
        $jugadores [$nombre] = $mano;
    }

    return $jugadores;
}


// Calcular el valor de la carta
function valorCarta($carta) {
    $valor = substr($carta, 0, -1);  // quita el último caracter
    if (in_array($valor, ['J', 'Q', 'K']))
        return 0.5;

    return floatval($valor); // en decimal float
}


// Calcular la puntuación total
function puntuacion($mano){
    $total = 0;
    
    foreach ($mano as $carta){
        $total += valorCarta($carta);
    }

    return $total;
}


// Mostrar cartas en una tabla HTML
function mostrar_cartas($jugadores) { 
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse; text-align:center;'>";
    echo "<tr><th>Jugador</th><th>Cartas</th></tr>";

    foreach ($jugadores as $nombre => $mano) {
        echo "<tr>";
        echo "<td><strong>$nombre</strong></td>";
        echo "<td>";

        // Mostrar cada carta como imagen
        foreach ($mano as $carta) {
            echo "<img src='images/$carta.png' alt='$carta' width='80' style='margin:5px;'>";
        }

        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

?>
