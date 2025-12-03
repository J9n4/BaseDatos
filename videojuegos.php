<?php
// 1. SEGURIDAD Y CONEXIÓN
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = ""; 

// 2. LÓGICA PHP PARA PROCESAR LOS FORMULARIOS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        
        // A) AGREGAR (CREATE)
        if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
            $sql = "EXEC sp_Videojuegos_Crear ?, ?, ?, ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $_POST['titulo'], 
                $_POST['plataforma'], 
                $_POST['precio'], 
                $_POST['stock']
            ]);
            $mensaje = "✅ Videojuego agregado correctamente."; 
            $tipo_mensaje = "success";
        }
        
        // B) MODIFICAR (UPDATE)
        elseif (isset($_POST['accion']) && $_POST['accion'] == 'modificar') {
            $sql = "EXEC sp_Videojuegos_Actualizar ?, ?, ?, ?, ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $_POST['id_juego'], 
                $_POST['titulo'], 
                $_POST['plataforma'], 
                $_POST['precio'], 
                $_POST['stock']
            ]);
            $mensaje = "✅ Videojuego actualizado correctamente."; 
            $tipo_mensaje = "success";
        }
        
        // C) ELIMINAR (DELETE)
        elseif (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
            $sql = "EXEC sp_Videojuegos_Eliminar @id_juego = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_POST['id_juego']]);
            $mensaje = "🗑️ Videojuego eliminado."; 
            $tipo_mensaje = "success";
        }

    } catch (PDOException $e) {
        $mensaje = "❌ Error: " . $e->getMessage(); 
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Videojuegos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🏠 Inicio</a>
        <div class="user-info">Inventario de Videojuegos 🕹️</div>
    </div>

    <div class="container">
        
        <?php if($mensaje): ?>
            <div class="msg-box <?php echo ($tipo_mensaje == 'success') ? 'msg-success' : 'msg-error'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <button class="btn-action btn-add" data-target="form-agregar" style="background-color: #6f42c1;">➕ Nuevo Juego</button>
            <button class="btn-action btn-edit" data-target="form-modificar">✏️ Modificar Juego</button>
            <button class="btn-action btn-del" data-target="form-eliminar">🗑️ Quitar Juego</button>
        </div>

        <div id="form-agregar" class="form-section form-box" style="border-top: 5px solid #6f42c1;">
            <h3>Nuevo Videojuego</h3>
            <form method="POST">
                <input type="hidden" name="accion" value="agregar">
                
                <label>Título:</label>
                <input type="text" name="titulo" placeholder="Ej: Super Mario" required style="width:100%; margin-bottom:10px;">
                
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Plataforma:</label>
                        <input type="text" name="plataforma" placeholder="Ej: Switch" required style="width:100%;">
                    </div>
                    <div style="flex:1;">
                        <label>Precio ($):</label>
                        <input type="number" step="0.01" name="precio" placeholder="0.00" required style="width:100%;">
                    </div>
                    <div style="flex:1;">
                        <label>Stock:</label>
                        <input type="number" name="stock" placeholder="Cantidad" required style="width:100%;">
                    </div>
                </div>
                
                <button type="submit" class="btn-login" style="background:#6f42c1; margin-top:15px;">Guardar en Inventario</button>
            </form>
        </div>

        <div id="form-modificar" class="form-section form-box" style="border-top: 5px solid #007bff;">
            <h3>Modificar Videojuego</h3>
            <p style="font-size:0.9rem; color:#666;">Selecciona un juego de la tabla inferior (botón lápiz).</p>
            <form method="POST">
                <input type="hidden" name="accion" value="modificar">
                
                <label>ID Juego:</label>
                <input type="number" id="mod_id_juego" name="id_juego" readonly style="width:100px; background:#e9ecef; margin-bottom:10px;">
                
                <label>Título:</label>
                <input type="text" id="mod_titulo" name="titulo" required style="width:100%; margin-bottom:10px;">
                
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Plataforma:</label>
                        <input type="text" id="mod_plataforma" name="plataforma" required style="width:100%;">
                    </div>
                    <div style="flex:1;">
                        <label>Precio:</label>
                        <input type="number" step="0.01" id="mod_precio" name="precio" required style="width:100%;">
                    </div>
                    <div style="flex:1;">
                        <label>Stock:</label>
                        <input type="number" id="mod_stock" name="stock" required style="width:100%;">
                    </div>
                </div>
                
                <button type="submit" class="btn-login" style="margin-top:15px;">Actualizar Datos</button>
            </form>
        </div>

        <div id="form-eliminar" class="form-section form-box" style="border-top: 5px solid #dc3545;">
            <h3>Eliminar Videojuego</h3>
            <form method="POST" onsubmit="return confirm('¿Confirma eliminar este juego del inventario?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="number" id="del_id" name="id_juego" placeholder="ID Juego" required style="width:100%; margin-bottom:15px;">
                <button type="submit" class="btn-login" style="background:#dc3545;">🗑️ Eliminar Definitivamente</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Plataforma</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "EXEC sp_Videojuegos_Leer"; 
                        $stmt = $conn->query($sql);

                        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Convertimos a JSON para JS
                            $json = htmlspecialchars(json_encode($fila), ENT_QUOTES, 'UTF-8');
                            
                            echo "<tr>";
                            echo "<td>" . $fila['id_juego'] . "</td>";
                            echo "<td><b>" . $fila['titulo'] . "</b></td>";
                            echo "<td>" . $fila['plataforma'] . "</td>";
                            echo "<td>$" . number_format($fila['precio'], 2) . "</td>";
                            
                            $estiloStock = ($fila['stock'] < 5) ? "color:red; font-weight:bold;" : "color:green;";
                            echo "<td style='$estiloStock'>" . $fila['stock'] . "</td>";
                            
                            echo "<td style='text-align:center;'>
                                    <button class='btn-row-edit' data-json='$json'>✏️</button>
                                    <button class='btn-row-delete' data-id='" . $fila['id_juego'] . "'>🗑️</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } catch (Exception $e) {
                        echo "<tr><td colspan='6'>No hay datos disponibles.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="js/main.js"></script>

</body>
</html>