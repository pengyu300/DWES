<?php

// Permite usar funciones de otro fichero PHP
require "media7fun.php";

// Comprobamos que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger y limpiar los datos del formulario
    $nombre1 = limpiar_campo($_POST["nombre1"]);
    $nombre2 = limpiar_campo($_POST["nombre2"]);
    $nombre3 = limpiar_campo($_POST["nombre3"]);
    $nombre4 = limpiar_campo($_POST["nombre4"]);
    $numcartas = limpiar_campo($_POST["numcartas"]);
    $apuesta = limpiar_campo($_POST["apuesta"]);

    // Guardar los nombres introducidos en un array
    $nombres = array($nombre1, $nombre2, $nombre3, $nombre4);

    // Repartir
    $jugadores = repartirJugadores($nombres, $numcartas);

    // Mostrar
    echo "<h2>Cartas repartidas</h2>";
    mostrar_cartas($jugadores);
}
?>
