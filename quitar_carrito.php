<?php
require_once 'config/sesion.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$index = (int)($_POST['index'] ?? -1);

if (isset($_SESSION['carrito'][$index])) {

    unset($_SESSION['carrito'][$index]);

    $_SESSION['carrito'] =
        array_values($_SESSION['carrito']);
}

header('Location: carrito.php');
exit;
