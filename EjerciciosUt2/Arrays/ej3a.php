<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Array - ej3a</title>
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
    <h2>Primeros 20 números binarioss</h2>
    <h4>Programa ej3a.php: definir un array y almacenar los 20 primeros números binarios.</h4>
    <table>
        <tr>
            <th>Índice</th>
            <th>Binario</th>
            <th>Octal</th>
        </tr>

        <?php
        // 1. Crear un array con los 20 primeros números binarios
        $binarios = array();
        for ($i = 0; $i < 20; $i++) 
        {
            //convierte decimal a binario
            $binarios[$i] = decbin($i);
        }

        // 2. Mostrar el array en tabla
        $suma = 0;
        for ($i = 0; $i < count($binarios); $i++) 
        {
            $binario = $binarios[$i];
            $octal = decoct($i); //convierte decimal a octal
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td>$binario</td>";
            echo "<td>$octal</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>

