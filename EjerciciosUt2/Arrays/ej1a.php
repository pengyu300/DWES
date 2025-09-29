<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ej1a - Números Impares</title>
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
    <h2>Primeros 20 números impares</h2>
    <table>
        <tr>
            <th>Índice</th>
            <th>Valor</th>
            <th>Suma</th>
        </tr>

        <?php
        // 1. Crear un array con los 20 primeros números impares
        $impares = array();
        for ($i = 0; $i < 20; $i++) {
            $impares[$i] = 2 * $i + 1;
        }

        // 2. Mostrar el array en tabla
        $suma = 0;
        for ($i = 0; $i < count($impares); $i++) {
            $suma += $impares[$i];
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td>{$impares[$i]}</td>";
            echo "<td>$suma</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

