<?php
// logout.php
// ============================================================
// Cierre de Sesión - Paso 3
// ============================================================
require_once 'config/sesion.php';
cerrarSesion();
header('Location: login.php');
exit;
