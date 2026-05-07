<?php
// gestion.php
// ============================================================
// Gestión de Inventario - Almacena en BD (Paso 3)
// AJAX para buscar producto por nombre/ID
// ============================================================
require_once 'config/conexion.php';
require_once 'config/sesion.php';

// Solo admin y editor pueden gestionar inventario
requiereRol('login.php', 'admin', 'editor');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="formularios.css">
    <style>
        label.error { color:#e74c3c; font-size:0.85em; margin-top:5px; font-weight:normal; }
        input.error { border:2px solid #e74c3c; background-color:#fadbd8; }

        /* Resultados AJAX */
        #resultados-busqueda {
            background:white;
            border:2px solid #1abc9c;
            border-radius:8px;
            max-width:450px;
            margin:0 auto 20px;
            display:none;
            text-align:left;
            overflow:hidden;
            box-shadow:0 4px 10px rgba(0,0,0,0.08);
        }
        .resultado-item {
            padding:10px 15px;
            cursor:pointer;
            border-bottom:1px solid #f0f4f8;
            transition:background 0.2s;
        }
        .resultado-item:hover { background:#f0f4f8; }
        .resultado-item strong { color:#2c3e50; }
        .resultado-item span   { color:#7f8c8d; font-size:0.85em; margin-left:8px; }

        /* Mensaje de respuesta */
        #msg-respuesta {
            max-width:450px;
            margin:0 auto 10px;
            padding:12px;
            border-radius:8px;
            display:none;
            font-weight:600;
        }
        .msg-ok  { background:#d5f5e3; color:#1e8449; }
        .msg-err { background:#fadbd8; color:#c0392b; }

        /* Sesión activa */
        .sesion-info {
            background:#2c3e50;
            color:white;
            padding:8px 20px;
            font-size:0.85em;
            text-align:right;
        }
    </style>
</head>
<body>
<!-- Banner de sesión activa (Paso 3) -->
<div class="sesion-info">
    Sesión activa: <strong><?= htmlspecialchars($_SESSION['nombre_usuario']) ?></strong>
    | Rol: <strong><?= htmlspecialchars($_SESSION['rol']) ?></strong>
    | <a href="logout.php" style="color:#1abc9c;">Cerrar sesión</a>
</div>

<header>
    <h1>Gestión de Inventario</h1>
</header>
<nav>
    <a href="index.html">Inicio</a>
    <a href="catalogo.php">Catálogo</a>
    <?php if (tieneRol('admin')): ?>
        <a href="gestion_usuarios.php">Gestión Usuarios</a>
    <?php endif; ?>
    <a href="logout.php">Cerrar Sesión</a>
</nav>
<main>
    <h2>Altas, Modificaciones y Eliminación</h2>

    <!-- Buscador AJAX de producto por nombre o ISBN (Paso 3) -->
    <div style="max-width:450px;margin:0 auto 10px;text-align:left;">
        <label for="buscador">Buscar Libro (nombre o ISBN):</label>
        <input type="text" id="buscador" placeholder="Escribe para buscar y autocompletar...">
    </div>
    <div id="resultados-busqueda"></div>

    <div id="msg-respuesta"></div>

    <form id="formularioGestion" action="#" method="POST">
        <fieldset>
            <legend>Datos del Libro/Producto</legend>

            <label for="id_producto">ID:</label>
            <input type="text" id="id_producto" name="id_producto">

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo">

            <label for="autor">Autor:</label>
            <input type="text" id="autor" name="autor">

            <label for="precio">Precio ($):</label>
            <input type="number" id="precio" name="precio" step="0.01">

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" value="0" min="0">

            <!-- ID interno para modificaciones/bajas -->
            <input type="hidden" id="libro_id" name="libro_id" value="">

            <label for="accion">Acción a realizar:</label>
            <select id="accion" name="accion">
                <option value="alta">Dar de Alta</option>
                <option value="modificar">Modificar</option>
                <option value="eliminar">Eliminar (Baja)</option>
            </select>

            <button type="submit">Ejecutar Acción</button>
        </fieldset>
    </form>
</main>
<footer>
    <p>&copy; 2026 BiblioTec.</p>
    <a href="https://validator.w3.org/nu/?doc=https://zabdi-bucio.github.io/Libreria-Web/gestion.php" target="_blank">
        <img src="img/valid_HTML5.png" alt="¡HTML 5 Válido!" height="31" width="88" style="border:0;">
    </a>
    <a href="https://jigsaw.w3.org/css-validator/validator?uri=https://zabdi-bucio.github.io/Libreria-Web/gestion.php" target="_blank">
        <img src="img/valid_CSS.png" alt="¡CSS Válido!" height="31" width="88" style="border:0;">
    </a>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
$(document).ready(function(){

    // ================================================================
    // AJAX: Buscador de productos por nombre / ISBN (Paso 3)
    // ================================================================
    let timerBusqueda;
    $("#buscador").on("input", function(){
        clearTimeout(timerBusqueda);
        const q = $(this).val().trim();
        if (q.length < 2) { $("#resultados-busqueda").hide().empty(); return; }

        timerBusqueda = setTimeout(function(){
            $.ajax({
                url: 'api/libros.php',
                method: 'GET',
                data: { accion: 'buscar', q: q },
                success: function(resp){
                    const $res = $("#resultados-busqueda").empty();
                    if (resp.exito && resp.datos.length) {
                        resp.datos.forEach(function(libro){
                            $res.append(
                                `<div class="resultado-item" data-id="${libro.id}"
                                      data-isbn="${libro.isbn}" data-titulo="${libro.titulo}"
                                      data-autor="${libro.autor}" data-precio="${libro.precio}"
                                      data-stock="${libro.stock}">
                                    <strong>${libro.titulo}</strong>
                                    <span>${libro.autor}</span>
                                    <span>ISBN: ${libro.isbn}</span>
                                    <span>$${parseFloat(libro.precio).toFixed(2)}</span>
                                 </div>`
                            );
                        });
                        $res.show();
                    } else {
                        $res.hide();
                    }
                },
                dataType: 'json'
            });
        }, 400);
    });

    // Autocompletar formulario al seleccionar un resultado AJAX
    $(document).on("click", ".resultado-item", function(){
        const d = $(this).data();
        $("#libro_id").val(d.id);
        $("#id_producto").val(d.isbn);
        $("#titulo").val(d.titulo);
        $("#autor").val(d.autor);
        $("#precio").val(d.precio);
        $("#stock").val(d.stock);
        $("#resultados-busqueda").hide().empty();
        $("#buscador").val('');
    });

    // Cerrar resultados al hacer clic fuera
    $(document).on("click", function(e){
        if (!$(e.target).closest("#buscador, #resultados-busqueda").length){
            $("#resultados-busqueda").hide();
        }
    });

    // ================================================================
    // Validación del formulario (jQuery Validate)
    // ================================================================
    $("#formularioGestion").validate({
        rules: {
            id_producto: { required: true, minlength: 4 },
            titulo:      { required: true, minlength: 2 },
            autor:       { required: true },
            precio:      { required: true, number: true, min: 1 }
        },
        messages: {
            id_producto: {
                required:  "El ID es obligatorio.",
                minlength: "Debe tener al menos 4 caracteres."
            },
            titulo: {
                required:  "Por favor, ingresa el título.",
                minlength: "El título es muy corto."
            },
            autor:  "El autor es obligatorio.",
            precio: {
                required: "Ingresa un precio.",
                number:   "Por favor ingresa un número válido.",
                min:      "El precio debe ser mayor a 0."
            }
        },

        // ================================================================
        // AJAX: Guardar / actualizar / eliminar en la BD (Paso 3)
        // ================================================================
        submitHandler: function(){
            const accion    = $("#accion").val();
            const libroId   = parseInt($("#libro_id").val()) || 0;
            const isbn      = $("#id_producto").val().trim();
            const titulo    = $("#titulo").val().trim();
            const autor     = $("#autor").val().trim();
            const precio    = parseFloat($("#precio").val());
            const stock     = parseInt($("#stock").val()) || 0;
            const $msg      = $("#msg-respuesta");

            function mostrarMsg(texto, ok){
                $msg.text(texto)
                    .removeClass('msg-ok msg-err')
                    .addClass(ok ? 'msg-ok' : 'msg-err')
                    .show();
                setTimeout(function(){ $msg.fadeOut(600); }, 3500);
            }

            if (accion === 'alta') {
                $.ajax({
                    url: 'api/libros.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ isbn, titulo, autor, precio, stock }),
                    success: function(r){ mostrarMsg(r.mensaje, r.exito); },
                    dataType: 'json'
                });

            } else if (accion === 'modificar') {
                if (!libroId) { mostrarMsg('Usa el buscador para seleccionar el libro.', false); return; }
                $.ajax({
                    url: 'api/libros.php',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: libroId, titulo, autor, precio, stock }),
                    success: function(r){ mostrarMsg(r.mensaje, r.exito); },
                    dataType: 'json'
                });

            } else if (accion === 'eliminar') {
                if (!libroId) { mostrarMsg('Usa el buscador para seleccionar el libro.', false); return; }
                if (!confirm(`¿Eliminar "${titulo}" del inventario?`)) return;
                $.ajax({
                    url: 'api/libros.php',
                    method: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: libroId }),
                    success: function(r){
                        mostrarMsg(r.mensaje, r.exito);
                        if (r.exito) $("#formularioGestion")[0].reset();
                    },
                    dataType: 'json'
                });
            }
        }
    });
});
</script>
</body>
</html>
