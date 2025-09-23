<HTML>
<HEAD><TITLE> EJ1-Conversion IP Decimal a Binario </TITLE></HEAD>
<BODY>
<?php
$ip = "10.33.161.2";

// Separar la IP en octetos
$octetos = explode('.', $ip);
// Inicializar vacío
$ip_binario = '';

// Convertir cada octeto a binario
for ($i = 0; $i < 4; $i++):
    if ($i > 0) {
        //Agregar un punto antes de cada octeto
        $ip_binario .= '.';
    }
    $ip_binario .= sprintf('%08b', $octetos[$i]);
endfor;

printf("IP %s en binario es %s <br/>", $ip, $ip_binario);
?>
</BODY>
</HTML>
