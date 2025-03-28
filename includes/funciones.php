<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'conexion.php'; // Incluir la conexión a la BD

/**
 * Función para limpiar y validar datos
 */
function limpiarDatos($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

/**
 * Función para validar si una cédula ecuatoriana es válida
 */
function validarCedulaEcuatoriana($cedula) {
    if (strlen($cedula) !== 10 || !ctype_digit($cedula)) {
        return false;
    }

    $digito_verificador = (int) $cedula[9];
    $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    $suma = 0;

    for ($i = 0; $i < 9; $i++) {
        $valor = (int) $cedula[$i] * $coeficientes[$i];
        $suma += ($valor >= 10) ? $valor - 9 : $valor;
    }

    $decena_superior = ceil($suma / 10) * 10;
    $verificador_calculado = ($decena_superior - $suma) % 10;

    return $verificador_calculado === $digito_verificador;
}
?>


