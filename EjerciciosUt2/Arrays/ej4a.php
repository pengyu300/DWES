<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Array - ej4a</title>
    <style>
        table {
            border-collapse: collapse;
            width: 300px;
            text-align: center;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h4>Programa ej4a.php: Números binarios en orden inverso</h4>
    <table>
        <tr>
            <th>Índice</th>
            <th>Binario</th>
        </tr>

        <?php
        // 1. Array con los 20 primeros números binarios
        $binarios = array();
        for ($i = 0; $i < 20; $i++) {
            $binarios[$i] = decbin($i);
        }

        // 2. Crear un nuevo array con los binarios en orden inverso
        $binariosInvertidos = array_reverse($binarios);

        // 3. Mostrar el array invertido
        for ($i = 0; $i < count($binariosInvertidos); $i++) {
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td>{$binariosInvertidos[$i]}</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
