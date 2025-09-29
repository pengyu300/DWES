<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Arrays - ej2a</title>
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

        // variables
        $sumaPares = 0;
        $sumaImpares = 0;
        $contarPares = 0;
        $contarImpares = 0;

        // 2. Mostrar tabla y acumular sumas para medias
        $suma = 0;
        for ($i = 0; $i < count($impares); $i++) {
            $valor = $impares[$i];
            $suma += $valor;

            // según el índice (par o impar) sumar los números
            // par
            if ($i % 2 == 0)
            {
                $sumaPares += $valor;
                $contarPares++;
            }
            //impar
            else{
                $sumaImpares += $valor;
                $contarImpares++;
            }

            // Mostrar fila
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td>$valor</td>";
            echo "<td>$suma</td>";
            echo "</tr>";

        }
        
        // 3. Calcular medias
        $mediaPares = $sumaPares / $contarPares;
        $mediaImpares = $sumaImpares / $contarImpares;

        echo "Media en posición pares: $mediaPares<br>";
        echo "Media en posición impares: $mediaImpares<br>";
        ?>
    </table>
</body>
</html>


