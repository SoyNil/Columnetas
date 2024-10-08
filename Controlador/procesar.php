<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cantidadColumnetas = (int)$_POST['cantidadColumnetas'];
    $pisos = (int)$_POST['pisos'];

    // Inicializar arrays
    $baseArray = [];
    $alturaArray = [];
    $tipoAceroArray = [];
    $cantidadAceroArray = [];

    // Recorrer y llenar arrays con los datos enviados por el formulario
    for ($i = 0; $i < $cantidadColumnetas; $i++) {
        $baseArray[] = (float)$_POST['base'][$i]/(25);
        $alturaArray[] = (float)$_POST['altura'][$i]/25;
        $tipoAceroArray[] = (float)$_POST['tipoAcero'][$i];
        $cantidadAceroArray[] = (float)$_POST['cantidadAcero'][$i];
    }
    echo "<pre>";
    print_r($baseArray);
    print_r($alturaArray);
    print_r($tipoAceroArray);
    echo "</pre>";
    $valores = ['a', 'b', 'c', 'd','e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 
    'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    $base = $baseArray[0];
    $altura = $alturaArray[0];
    $altura = max($alturaArray); // Obtener el valor máximo de altura
    $alturaTabla = $altura + 2.5; // Ahora $alturaTabla se basa en el valor máximo
    $baseTabla = $baseArray[0] + 4;
    $contenido_dxf = "";
    $contenido_dxf .= "0\nSECTION\n2\nENTITIES\n";
    // Definir el offset para dibujar los cuadrados
    $offsetX = 3; // Offset para el eje X
    $radio = 0.02; // Radio para los arcos

    // Generar cuadrados para cada columneta
    for ($i = 0; $i < $cantidadColumnetas; $i++) {
        $contenido_dxf .= "0\nPOLYLINE\n8\n0\n66\n1\n70\n8\n62\n5\n";
        
        // Definir los vértices del cuadrado
        $vertices = array(
            array($offsetX, 1.2), // Esquina inferior izquierda
            array($offsetX + $baseArray[$i], 1.2), // Esquina inferior derecha
            array($offsetX + $baseArray[$i], $alturaArray[$i] + 1.2), // Esquina superior derecha
            array($offsetX, $alturaArray[$i] + 1.2), // Esquina superior izquierda
            array($offsetX, 1.2) // Cerrar el polígono
        );

        // Agregar los vértices al contenido DXF
        foreach ($vertices as $vertex) {
            $x = $vertex[0];
            $y = $vertex[1];
            $contenido_dxf .= "0\nVERTEX\n8\n0\n10\n$x\n20\n$y\n30\n0\n"; // Z=0 para 2D
        }
        $contenido_dxf .= "0\nSEQEND\n";
        $yOffset= 0.05;
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n7\n10\n" . $offsetX . "\n20\n" . (1.2+$alturaArray[$i] + $yOffset) . "\n11\n" . ($baseArray[$i] + $offsetX) . "\n21\n" . (1.2+$alturaArray[$i] + $yOffset) . "\n";
        $texBasex = ($baseArray[$i] * 25); 
        $contenido_dxf .= "0\nTEXT\n8\n0\n62\n7\n10\n" . (($baseArray[$i] / 2) + $offsetX) . "\n20\n" . ((1.2+$alturaArray[$i] + $yOffset + 0.07)) . "\n40\n0.05\n1\n$texBasex\n";
        $xOffset = ($baseArray[$i] + $offsetX) + 0.05;
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n7\n10\n$xOffset\n20\n1.2\n11\n$xOffset\n21\n" . ($alturaArray[$i] + 1.2) . "\n";
        $texBasey = ($alturaArray[$i] * 25); 
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($xOffset + 0.02) . "\n20\n" . (($alturaArray[$i] / 2) + 1.2) . "\n40\n0.05\n1\n$texBasey\n";
        // Crear cuadrado interno y arcos
        $p1 = array($offsetX + 0.08, 1.28);
        $p2 = array($offsetX + $baseArray[$i] - 0.08, 1.28);
        $p3 = array($offsetX + $baseArray[$i] - 0.08, $alturaArray[$i] +1.12);
        $p4 = array($offsetX + 0.08, $alturaArray[$i] +1.12);
        // Líneas del cuadrado interno
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . ($p1[0] + $radio) . "\n20\n$p1[1]\n11\n" . ($p2[0] - $radio) . "\n21\n$p2[1]\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . $p2[0] . "\n20\n" . ($p2[1] + $radio) . "\n11\n" . $p3[0] . "\n21\n" . ($p3[1] - $radio) . "\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . ($p3[0] - $radio) . "\n20\n" . $p3[1] . "\n11\n" . ($p4[0] + $radio) . "\n21\n" . $p4[1] . "\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . $p4[0] . "\n20\n" . ($p4[1] - $radio) . "\n11\n" . $p1[0] . "\n21\n" . ($p1[1] + $radio) . "\n";
        // Arcos del cuadrado interno
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p1[0] + $radio) . "\n20\n" . ($p1[1] + $radio) . "\n40\n$radio\n50\n180\n51\n270\n";
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p2[0] - $radio) . "\n20\n" . ($p2[1] + $radio) . "\n40\n$radio\n50\n270\n51\n0\n";
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p3[0] - $radio) . "\n20\n" . ($p3[1] - $radio) . "\n40\n$radio\n50\n0\n51\n90\n";
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p4[0] + $radio) . "\n20\n" . ($p4[1] - $radio) . "\n40\n$radio\n50\n90\n51\n180\n";
        // Crear cuadrado interno y arcos
        $p1 = array($offsetX + 0.10, 1.30); //Esquina inferior izquierda
        $p2 = array($offsetX + $baseArray[$i] - 0.10, 1.30); //Esquina inferior derecha
        $p3 = array($offsetX + $baseArray[$i] - 0.10, $alturaArray[$i] +1.10); //Esquina superior derecha
        $p4 = array($offsetX + 0.10, $alturaArray[$i] +1.10); //Esquina superior izquierda
        // Líneas del cuadrado interno
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . ($p1[0] + $radio) . "\n20\n$p1[1]\n11\n" . ($p2[0] - $radio) . "\n21\n$p2[1]\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . $p2[0] . "\n20\n" . ($p2[1] + $radio) . "\n11\n" . $p3[0] . "\n21\n" . ($p3[1] - $radio) . "\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . ($p3[0] - $radio) . "\n20\n" . $p3[1] . "\n11\n" . ($p4[0] + $radio) . "\n21\n" . $p4[1] . "\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n8\n10\n" . $p4[0] . "\n20\n" . ($p4[1] - $radio) . "\n11\n" . $p1[0] . "\n21\n" . ($p1[1] + $radio) . "\n";
        // Arcos del cuadrado interno
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p1[0] + $radio) . "\n20\n" . ($p1[1] + $radio) . "\n40\n$radio\n50\n180\n51\n270\n";
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p2[0] - $radio) . "\n20\n" . ($p2[1] + $radio) . "\n40\n$radio\n50\n270\n51\n0\n";
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p3[0] - $radio) . "\n20\n" . ($p3[1] - $radio) . "\n40\n$radio\n50\n0\n51\n90\n";
        $contenido_dxf .= "0\nARC\n8\n0\n62\n8\n10\n" . ($p4[0] + $radio) . "\n20\n" . ($p4[1] - $radio) . "\n40\n$radio\n50\n90\n51\n180\n";
        $vertices_gancho_izquierdo = [
            [$offsetX + $baseArray[$i] - 0.08-0.059, $alturaArray[$i] +1.12],
            [$offsetX + $baseArray[$i] - 0.08-0.059-($baseArray[$i]/3), ($alturaArray[$i] + 1.12)-($alturaArray[$i]/3)]
        ];
        
        $vertices_gancho_derecho = [
            [$offsetX + $baseArray[$i] - 0.08, $alturaArray[$i] +1.12-0.059],
            [$offsetX + $baseArray[$i] - 0.08-($baseArray[$i]/3), ($alturaArray[$i] + 1.12)-($alturaArray[$i]/3)-0.059]
        ];

        // Función para dibujar ganchos
        $dibujar_gancho = function ($vertices) use (&$contenido_dxf) {
            foreach ($vertices as $i => $vertex) {
                $x1 = $vertex[0];
                $y1 = $vertex[1];
                $x2 = $vertices[($i + 1) % count($vertices)][0];
                $y2 = $vertices[($i + 1) % count($vertices)][1];
                $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n62\n8\n";
            }
        };
        // Llamar a la función para dibujar ganchos
        $dibujar_gancho($vertices_gancho_izquierdo);
        $dibujar_gancho($vertices_gancho_derecho);

        $vertices_gancho_izquierdo = [
            [$offsetX + $baseArray[$i] - 0.10-0.039, $alturaArray[$i] +1.10],
            [$offsetX + $baseArray[$i] - 0.10+0.019-($baseArray[$i]/3), ($alturaArray[$i] + 1.12)-($alturaArray[$i]/3)+0.02]
        ];
        
        $vertices_gancho_derecho = [
            [$offsetX + $baseArray[$i] - 0.10, $alturaArray[$i] +1.10-0.039],
            [$offsetX + $baseArray[$i] - 0.10-($baseArray[$i]/3)+0.02, ($alturaArray[$i] + 1.12)-($alturaArray[$i]/3)-0.019]
        ];

        // Función para dibujar ganchos
        $dibujar_gancho = function ($vertices) use (&$contenido_dxf) {
            foreach ($vertices as $i => $vertex) {
                $x1 = $vertex[0];
                $y1 = $vertex[1];
                $x2 = $vertices[($i + 1) % count($vertices)][0];
                $y2 = $vertices[($i + 1) % count($vertices)][1];
                $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n62\n8\n";
            }
        };
        // Llamar a la función para dibujar ganchos
        $dibujar_gancho($vertices_gancho_izquierdo);
        $dibujar_gancho($vertices_gancho_derecho);
        
        // Crear cuadrado interno y arcos
        $verticesint = array(
            array($offsetX + 0.10, 1.30), // Esquina inferior izquierda
            array($offsetX + $baseArray[$i] - 0.10, 1.30), // Esquina inferior derecha
            array($offsetX + $baseArray[$i] - 0.10, $alturaArray[$i] + 1.10), // Esquina superior derecha
            array($offsetX + 0.10, $alturaArray[$i] + 1.10), // Esquina superior izquierda
            array($offsetX + 0.10, 1.30) // Esquina inferior izquierda
        );

        // Obtener el tipo de acero y calcular el radio
        $tipoAcero = $tipoAceroArray[$i];
        if ($tipoAcero == 0.0127 ) {
            $tipoAcero1 = "1/4";
        } elseif ($tipoAcero == 0.019039999999999998 ) {
            $tipoAcero1 = "3/8";
        } elseif ($tipoAcero == 0.0254 ) {
            $tipoAcero1 = "1/2";
        } elseif ($tipoAcero == 0.03174 ) {
            $tipoAcero1 = "5/8";
        } elseif ($tipoAcero == 0.0381 ) {
            $tipoAcero1 = "3/4";
        } elseif ($tipoAcero == 0.0508) {
            $tipoAcero1 = "1";
        }

        // Calcular el tipo de acero y el radio del círculo
        $radio_circulo = $tipoAcero;

        // Cálculo de la cantidad de círculos
        $cantidadCírculos = $cantidadAceroArray[$i];
        $ancho_x = $verticesint[1][0] - $verticesint[0][0];
        $espacio_x = $ancho_x / ($cantidadCírculos - 1);

        // Definir el punto de convergencia en la parte inferior (debajo del cuadrado)
        $punto_convergente_y_inferior = $verticesint[0][1] - 0.5; // Coordenada Y en la parte inferior
        $puntos_convergencia_inferior = [];

        // Círculos en la parte inferior
        $circulos_inferiores = [];
        for ($j = 0; $j < $cantidadCírculos; $j++) {
            $centro_x = $verticesint[0][0] + $j * $espacio_x;
            $centro_y = $verticesint[0][1] - $radio_circulo;

            // Crear el círculo en la parte inferior
            $contenido_dxf .= "0\nCIRCLE\n8\n0\n10\n$centro_x\n20\n$centro_y\n40\n$radio_circulo\n";

            // Guardar las coordenadas del círculo inferior
            $circulos_inferiores[] = array($centro_x, $centro_y);

            // Calcular el punto de convergencia en X para cada par de círculos inferiores
            $punto_convergente_x_inferior = $centro_x;
            $puntos_convergencia_inferior[] = array($punto_convergente_x_inferior, $punto_convergente_y_inferior);
        }

        // Círculos en la parte superior
        $circulos_superiores = [];
        for ($j = 0; $j < $cantidadCírculos; $j++) {
            $centro_x = $verticesint[0][0] + $j * $espacio_x;
            $centro_y = $verticesint[2][1] + $radio_circulo;

            // Crear el círculo en la parte superior
            $contenido_dxf .= "0\nCIRCLE\n8\n0\n10\n$centro_x\n20\n$centro_y\n40\n$radio_circulo\n";

            // Guardar las coordenadas del círculo superior
            $circulos_superiores[] = array($centro_x, $centro_y);
        }

        // Conectar cada círculo inferior y superior con el punto de convergencia inferior
        for ($j = 0; $j < $cantidadCírculos; $j++) {
            $centro_x_inferior = $circulos_inferiores[$j][0];
            $centro_y_inferior = $circulos_inferiores[$j][1];

            $centro_x_superior = $circulos_superiores[$j][0];
            $centro_y_superior = $circulos_superiores[$j][1];

            // Calcular el punto de convergencia para este par de círculos (en X será el mismo que el círculo inferior)
            $punto_convergente_x_inferior = $puntos_convergencia_inferior[$j][0] - 0.1;
            $punto_convergente_y_inferior = $puntos_convergencia_inferior[$j][1] + 0.26;

            // Crear la línea que conecta el círculo inferior con el punto de convergencia
            $contenido_dxf .= "0\nLINE\n8\n0\n62\n1\n10\n$centro_x_inferior\n20\n$centro_y_inferior\n11\n$punto_convergente_x_inferior\n21\n$punto_convergente_y_inferior\n";

            // Crear la línea que conecta el círculo superior con el mismo punto de convergencia
            $contenido_dxf .= "0\nLINE\n8\n0\n62\n1\n10\n$centro_x_superior\n20\n$centro_y_superior\n11\n$punto_convergente_x_inferior\n21\n$punto_convergente_y_inferior\n";
            $punto_convergente_x_txt = $punto_convergente_x_inferior-0.3;
            $contenido_dxf .= "0\nLINE\n8\n0\n62\n1\n10\n$punto_convergente_x_inferior\n20\n$punto_convergente_y_inferior\n11\n$punto_convergente_x_txt\n21\n$punto_convergente_y_inferior\n";
            $contenido_dxf .= "0\nTEXT\n8\n0\n62\n0\n10\n" . ($punto_convergente_x_txt) . "\n20\n" . ($punto_convergente_y_inferior+0.01) . "\n40\n0.05\n1\n2∅$tipoAcero1\"\n";
        }
        $cantidadCírculos1=$cantidadCírculos*2;
        $txt=$cantidadCírculos1."θ".$tipoAcero1;
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX) . "\n20\n" . ($alturaTabla - 1.9 - $altura) . "\n40\n0.1\n1\n$txt\"\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX) . "\n20\n" . ($alturaTabla - 2.1 - $altura) . "\n40\n0.1\n1\n1 1/4\"∅: 1@0.05, 4@0.10,\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX) . "\n20\n" . ($alturaTabla - 2.3 - $altura) . "\n40\n0.1\n1\nRst. @0.20 C/E\n";
        $var=$valores[$i % count($valores)];
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX+($baseArray[$i]/2)) . "\n20\n" . ($alturaTabla - 0.8) . "\n40\n0.1\n1\np$var\n";

        // Actualizar el offset para el siguiente cuadrado (ajustar según el tamaño deseado)
        $offsetX += $baseArray[$i] + 2; // Aumentar el offset por el tamaño de la base y un espacio adicional
    }
    //Tabla    
    // Crear líneas al final de cada terminación de base
    for ($i = 0; $i < $cantidadColumnetas; $i++) {
        if ($i == 0) {
            // Primera columneta
            $x1 = $baseTabla; // Posición actual de la base
            $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n0\n30\n0\n"; // Punto inicial de la línea
            $contenido_dxf .= "11\n$x1\n21\n" . (max($alturaArray) + 1.9) . "\n31\n0\n"; // Punto final de la línea
            $contenido_dxf .= "62\n1\n";
        } else {
            // Columnetas adicionales
            $baseTabla += $baseArray[$i] + 2; // Actualizar baseTabla
            $x1 = $baseTabla; // Posición actual de la base
            $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n0\n30\n0\n"; // Punto inicial de la línea
            $contenido_dxf .= "11\n$x1\n21\n" . (max($alturaArray) + 1.9) . "\n31\n0\n"; // Punto final de la línea
            $contenido_dxf .= "62\n1\n";
        }
    }
    $contenido_dxf .= "0\nPOLYLINE\n8\n0\n66\n1\n70\n8\n62\n1\n";
    $verticestabla = array(
        array(0, 0),
        array($baseTabla, 0),
        array($baseTabla, $alturaTabla),
        array(0, $alturaTabla),
        array(0, 0),
    );
    foreach ($verticestabla as $vertex) {
        $x = $vertex[0];
        $y = $vertex[1];
        $contenido_dxf .= "0\nVERTEX\n8\n0\n10\n$x\n20\n$y\n";
    }
    
    //TextoTablas
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.2) . "\n20\n" . ($alturaTabla - 0.3) . "\n40\n0.1\n1\nESCALA:\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.3) . "\n20\n" . ($alturaTabla - 0.5) . "\n40\n0.1\n1\n 1/25\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($baseTabla/2) . "\n20\n" . ($alturaTabla - 0.4) . "\n40\n0.1\n1\nCUADRO DE COLUMNA\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.2) . "\n20\n" . ($alturaTabla - 0.8) . "\n40\n0.1\n1\nNIVEL\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (1.15) . "\n20\n" . ($alturaTabla - 0.8) . "\n40\n0.1\n1\nCONCRETO\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (1) . "\n20\n" . ($alturaTabla - 1) . "\n40\n0.1\n1\nfc'(Kg/cm2)\n";
    // Altura de cada piso adicional (puedes ajustar esto según sea necesario)
    $alturaPisoAdicional = 0.2;
    for ($i = 1; $i <= $pisos; $i++) {
        $contenido_dxf .= "0\nTEXT\n8\n0\n";
        $contenido_dxf .= "10\n" . (0.2) . "\n20\n" . ($alturaTabla - 1.2 - ($i * $alturaPisoAdicional)) . "\n40\n0.1\n1\n" . ($i + 0) . "° PISO\n";
    }
    // Agregar el texto "AZOTEA" justo debajo del último piso
    $contenido_dxf .= "0\nTEXT\n8\n0\n";
    $contenido_dxf .= "10\n0.2\n20\n" . ($alturaTabla - 1.2 - (($pisos + 1) * $alturaPisoAdicional)) . "\n40\n0.1\n1\nAZOTEA\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (1.3) . "\n20\n" . ($alturaTabla - 1.8) . "\n40\n0.1\n1\n175\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.5) . "\n20\n" . ($alturaTabla - 2 - $altura) . "\n40\n0.1\n1\nESFUERZO Y\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.5) . "\n20\n" . ($alturaTabla - 2.2 - $altura) . "\n40\n0.1\n1\nESTRIBAJE\n";
    //Fin
    $medidasFinalAlto = $altura4 * $escala;
    $mediadaFinalBase = $base * $escala;
    //Agregar el texto "medidas del grafico" dentro del rectángulo

    $contenido_dxf .= "0\nSEQEND\n";
    //Verticales
    $contenido_dxf .= "62\n1\n";
    $verticestablaH = array(
        array(1, 1),
        array(1, $alturaTabla),
    );
    foreach ($verticestablaH as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticestablaH[($i + 1) % count($verticestablaH)][0];
        $y2 = $verticestablaH[($i + 1) % count($verticestablaH)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n62\n1\n";
    }
    $verticestablaH = array(
        array(2, 0),
        array(2, $alturaTabla - 0.6),
    );
    foreach ($verticestablaH as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticestablaH[($i + 1) % count($verticestablaH)][0];
        $y2 = $verticestablaH[($i + 1) % count($verticestablaH)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n62\n1\n";
    }
    //Horizontales
    $verticesVE = array(
        array(0, $alturaTabla - 0.6),
        array($baseTabla, $alturaTabla - 0.6),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0, $alturaTabla - 1.1),
        array($baseTabla, $alturaTabla - 1.1),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
    }
    $contenido_dxf .= "62\n1\n"; // Establecer el color rojo
    $verticesVE = array(
        array(0, 1),
        array($baseTabla, 1),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
    }
    $contenido_dxf .= "62\n1\n";
    $contenido_dxf .= "0\nENDSEC\n0\nEOF";
    $archivoDXF = 'Columna-rectangular-cuadrado.dxf';
    $rutaArchivo = __DIR__ . '/' . $archivoDXF;
    file_put_contents($rutaArchivo, $contenido_dxf);
    $rutaAutoCAD = 'C:\Program Files\Autodesk\AutoCAD 2021\acad.exe';
    $comando = 'start "" "' . $rutaAutoCAD . '" "' . $rutaArchivo . '"';
    // Ejecutar el comando
    shell_exec($comando);
    echo "AutoCAD iniciado con el archivo DXF basado en las dimensiones proporcionadas.";
}
?>
