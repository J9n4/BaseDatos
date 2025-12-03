<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if ($_POST['accion'] == 'agregar') {
            $sql = "EXEC sp_Clientes_Crear ?, ?, ?, ?";
            $conn->prepare($sql)->execute([$_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['telefono']]);
            $mensaje = "✅ Cliente agregado."; $tipo_mensaje = "success";
        }
        elseif ($_POST['accion'] == 'modificar') {
            $sql = "EXEC sp_Clientes_Actualizar ?, ?, ?, ?, ?";
            
            $conn->prepare($sql)->execute([$_POST['id_cliente'], $_POST['nombre'], $_POST['apellido'], $_POST['correo'], $_POST['telefono']]);
            $mensaje = "✅ Cliente actualizado."; $tipo_mensaje = "success";
        }
        elseif ($_POST['accion'] == 'eliminar') {
            $sql = "EXEC sp_Clientes_Eliminar @id_cliente = ?";
            $conn->prepare($sql)->execute([$_POST['id_cliente']]);
            $mensaje = "🗑️ Cliente eliminado."; $tipo_mensaje = "success";
        }
    } catch (PDOException $e) {
        $mensaje = "❌ Error: " . $e->getMessage(); $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        .action-buttons { display: flex; gap: 15px; margin-bottom: 20px; justify-content: center; }
        .btn-action { padding: 12px 25px; border: none; border-radius: 50px; font-weight: bold; cursor: pointer; color: white; transition: 0.2s; }
        .btn-action:hover { transform: translateY(-2px); }
        .btn-add { background: #28a745; } .btn-edit { background: #007bff; } .btn-del { background: #dc3545; }
        .form-section { display: none; margin-bottom: 30px; animation: slideDown 0.4s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .msg-box { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .msg-success { background: #d4edda; color: #155724; } .msg-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🏠 Inicio</a>
        <div class="user-info">Gestión de Clientes</div>
    </div>

    <div class="container">
        
        <?php if($mensaje): ?>
            <div class="msg-box <?php echo ($tipo_mensaje == 'success') ? 'msg-success' : 'msg-error'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <button class="btn-action btn-add" data-target="form-agregar">➕ Agregar Cliente</button>
            <button class="btn-action btn-edit" data-target="form-modificar">✏️ Modificar Cliente</button>
            <button class="btn-action btn-del" data-target="form-eliminar">🗑️ Quitar Cliente</button>
        </div>

        <div id="form-agregar" class="form-section form-box" style="border-top: 5px solid #28a745;">
            <h3>Nuevo Cliente</h3>
            <form method="POST">
                <input type="hidden" name="accion" value="agregar">
                <div style="display:flex; gap:10px;">
                    <input type="text" name="nombre" placeholder="Nombre" required style="flex:1;">
                    <input type="text" name="apellido" placeholder="Apellido" required style="flex:1;">
                </div>
                <input type="email" name="correo" placeholder="Correo" required style="width:100%; margin-top:10px;">
                <input type="text" name="telefono" placeholder="Teléfono" required style="width:100%; margin-top:10px; margin-bottom:15px;">
                <button type="submit" class="btn-login" style="background:#28a745;">Guardar Nuevo</button>
            </form>
        </div>

        <div id="form-modificar" class="form-section form-box" style="border-top: 5px solid #007bff;">
            <h3>Modificar Cliente</h3>
            <p style="font-size:0.9rem; color:#666;">Selecciona un registro de la tabla.</p>
            <form method="POST">
                <input type="hidden" name="accion" value="modificar">
                
                <label>ID:</label>
                <input type="number" id="mod_id_cliente" name="id_cliente" readonly style="width:100px; background:#e9ecef; margin-bottom:10px;">
                
                <div style="display:flex; gap:10px;">
                    <input type="text" id="mod_nombre" name="nombre" required style="flex:1;">
                    <input type="text" id="mod_apellido" name="apellido" required style="flex:1;">
                </div>
                <input type="email" id="mod_correo" name="correo" required style="width:100%; margin-top:10px;">
                <input type="text" id="mod_telefono" name="telefono" required style="width:100%; margin-top:10px; margin-bottom:15px;">
                <button type="submit" class="btn-login" style="background:#007bff;">Guardar Cambios</button>
            </form>
        </div>

        <div id="form-eliminar" class="form-section form-box" style="border-top: 5px solid #dc3545;">
            <h3>Eliminar Cliente</h3>
            <form method="POST" onsubmit="return confirm('¿Confirma eliminar?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="number" id="del_id" name="id_cliente" placeholder="ID Cliente" required style="width:100%; margin-bottom:15px;">
                <button type="submit" class="btn-login" style="background:#dc3545;">🗑️ Eliminar</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("EXEC sp_Clientes_Leer");
                    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        
                        $json = htmlspecialchars(json_encode($fila), ENT_QUOTES, 'UTF-8');
                        echo "<tr>";
                        echo "<td>{$fila['id_cliente']}</td>";
                        echo "<td>{$fila['nombre']} {$fila['apellido']}</td>";
                        echo "<td>{$fila['correo']}</td>";
                        echo "<td>{$fila['telefono']}</td>";
                        echo "<td style='text-align:center;'>
                                <button class='btn-row-edit' data-json='$json' style='border:none; background:transparent; font-size:1.2rem; cursor:pointer;'>✏️</button>
                                <button class='btn-row-delete' data-id='{$fila['id_cliente']}' style='border:none; background:transparent; font-size:1.2rem; cursor:pointer;'>🗑️</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/main.js"></script>

</body>
</html>