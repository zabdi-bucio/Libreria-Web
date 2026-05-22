<?php
require_once 'config/conexion.php';
require_once 'config/sesion.php';

// Solo admin y editor pueden gestionar inventario
requiereRol('login.php', 'admin', 'editor');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="formularios.css">
    <style>
        label.error { color:#e74c3c; font-size:0.85em; margin-top:5px; font-weight:normal; }
        input.error, textarea.error { border:2px solid #e74c3c; background-color:#fadbd8; }
        #resultados-busqueda {
            background:white; border:2px solid #1abc9c; border-radius:8px;
            max-width:450px; margin:0 auto 20px; display:none; text-align:left;
            overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.08);
        }
        .resultado-item { padding:10px 15px; cursor:pointer; border-bottom:1px solid #f0f4f8; transition:background 0.2s; }
        .resultado-item:hover { background:#f0f4f8; }
        .resultado-item strong { color:#2c3e50; }
        .resultado-item span   { color:#7f8c8d; font-size:0.85em; margin-left:8px; }
        .sesion-info { background:#2c3e50; color:white; padding:8px 20px; font-size:0.85em; text-align:right; }
    </style>
</head>
<body>
<div class="sesion-info">
    Sesión activa: <strong><?= htmlspecialchars($_SESSION['nombre_usuario']) ?></strong>
    | Rol: <strong><?= htmlspecialchars(ucfirst($_SESSION['rol'])) ?></strong>
    | <a href="logout.php" style="color:#1abc9c;">Cerrar sesión</a>
</div>

<header>
    <h1>Gestión de Inventario</h1>
</header>

<?php require_once 'config/nav.php'; ?>

<main>
    <h2>Altas, Modificaciones y Eliminación</h2>

    <div style="max-width:450px;margin:0 auto 10px;text-align:left;">
        <label for="buscador" style="font-weight:bold; color:#e74c3c;">1. Buscar Libro (Para modificar o eliminar):</label>
        <input type="text" id="buscador" placeholder="Escribe el nombre del libro o ID...">
    </div>
    <div id="resultados-busqueda"></div>

    <form id="formularioGestion" action="#" method="POST" autocomplete="off">
        <fieldset>
            <legend>2. Datos del Libro</legend>

            <label for="id_visible">ID (Automático):</label>
            <input type="text" id="id_visible" name="id_visible" readonly placeholder="Se asigna solo">

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="3" style="width:100%; border:2px solid #e0e6ed; border-radius:8px; padding:10px; font-family: inherit; margin-bottom: 15px;"></textarea>

            <label for="precio">Precio ($):</label>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <label for="stock">Stock disponible:</label>
            <input type="number" id="stock" name="stock" value="0" min="0" required>

            <input type="hidden" id="libro_id" name="libro_id" value="">

            <label for="accion" style="font-weight:bold; color:#1abc9c; margin-top:15px;">3. Acción a realizar:</label>
            <select id="accion" name="accion">
                <option value="alta">Dar de Alta (Libro Nuevo)</option>
                <option value="modificar">Modificar Libro Seleccionado</option>
                <option value="eliminar">Eliminar Libro (Baja)</option>
            </select>

            <button type="submit" style="margin-top:15px; font-size:1.1em; padding:10px;">Ejecutar Acción</button>
        </fieldset>
    </form>
</main>

<footer>
    <p>&copy; 2026 BiblioTec.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
$(document).ready(function(){

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
                                      data-titulo="${libro.titulo}"
                                      data-descripcion="${libro.descripcion || ''}" 
                                      data-precio="${libro.precio}"
                                      data-stock="${libro.stock}">
                                    <strong>${libro.titulo}</strong>
                                    <span>ID: ${libro.id}</span>
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

    $(document).on("click", ".resultado-item", function(){
        const d = $(this).data();
        $("#libro_id").val(d.id);
        $("#id_visible").val(d.id);
        $("#titulo").val(d.titulo);
        $("#descripcion").val(d.descripcion);
        $("#precio").val(d.precio);
        $("#stock").val(d.stock);
        
        $("#resultados-busqueda").hide().empty();
        $("#buscador").val('');
        alert("¡Libro '" + d.titulo + "' cargado! Ahora puedes modificarlo o eliminarlo.");
    });

    $(document).on("click", function(e){
        if (!$(e.target).closest("#buscador, #resultados-busqueda").length){
            $("#resultados-busqueda").hide();
        }
    });

    $("#formularioGestion").validate({
        rules: {
            titulo:      { required: true },
            precio:      { required: true, number: true }
        },
        submitHandler: function(){
            const accion    = $("#accion").val();
            const libroId   = parseInt($("#libro_id").val()) || 0;
            const titulo    = $("#titulo").val().trim();
            const descripcion = $("#descripcion").val().trim();
            const precio    = parseFloat($("#precio").val());
            const stock     = parseInt($("#stock").val()) || 0;

            if (accion === 'alta') {
                $.ajax({
                    url: 'api/libros.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ titulo, descripcion, precio, stock }),
                    success: function(r){ 
                        alert("✅ ÉXITO ALTA: " + r.mensaje); 
                        $("#formularioGestion")[0].reset();
                    },
                    error: function(xhr){ alert("❌ ERROR ALTA: " + (xhr.responseJSON ? xhr.responseJSON.mensaje : "Error en servidor.")); },
                    dataType: 'json'
                });

            } else if (accion === 'modificar') {
                if (!libroId) { alert("¡ALTO! No has seleccionado ningún libro."); return; }
                $.ajax({
                    url: 'api/libros.php',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: libroId, titulo, descripcion, precio, stock }),
                    success: function(r){ 
                        alert("✅ ÉXITO MODIFICAR: " + r.mensaje); 
                        $("#formularioGestion")[0].reset();
                        $("#libro_id").val('');
                    },
                    error: function(xhr){ alert("❌ ERROR MODIFICAR: " + (xhr.responseJSON ? xhr.responseJSON.mensaje : "Error.")); },
                    dataType: 'json'
                });

            } else if (accion === 'eliminar') {
                if (!libroId) { alert("¡ALTO! No has seleccionado ningún libro."); return; }
                if (!confirm(`¿Estás segura de eliminar "${titulo}"?`)) return;
                
                $.ajax({
                    url: 'api/libros.php',
                    method: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: libroId }),
                    success: function(r){
                        alert("✅ ÉXITO ELIMINAR: " + r.mensaje);
                        $("#formularioGestion")[0].reset();
                        $("#libro_id").val('');
                    },
                    error: function(xhr){ alert("❌ ERROR ELIMINAR: " + (xhr.responseJSON ? xhr.responseJSON.mensaje : "Error.")); },
                    dataType: 'json'
                });
            }
        }
    });
});
</script>
</body>
</html>
