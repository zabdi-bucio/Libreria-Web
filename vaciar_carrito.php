<?php
require_once 'config/sesion.php';

$_SESSION['carrito'] = [];

header('Location: carrito.php');
exit;