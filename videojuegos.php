<?php
// videojuegos.php CORREGIDO
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
        // A) AGREGAR
        if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
            // Fíjate que aquí no falte nada antes de $sql
            $sql = "EXEC sp_Videojuegos_Crear ?, ?, ?, ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $_POST['titulo'], 
                $_POST['plataforma'], 
                $_POST['precio'], 
                $_POST['stock']
            ]);
            $mensaje = "✅ Juego agregado."; 
            $tipo_mensaje = "success";
        }
        
        // B) MODIFICAR
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
            $mensaje = "✅ Juego actualizado."; 
            $tipo_mensaje = "success";
        }
        
        // C) ELIMINAR
        elseif (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
            $sql = "EXEC sp_Videojuegos_Eliminar @id_juego = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_POST['id_juego']]);
            $mensaje = "🗑️ Juego eliminado."; 
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
    <title>Videojuegos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🏠 Inicio</a>
        <div class="user-info">Videojuegos 🕹️</div>
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
                <input type="text" name="titulo" placeholder="Título" required style="width:100%; margin-bottom:10px;">
                <div style="display:flex; gap:10px;">
                    <input type="text" name="plataforma" placeholder="Plataforma" required style="flex:1;">
                    <input type="number" step="0.01" name="precio" placeholder="Precio" required style="flex:1;">
                    <input type="number" name="stock" placeholder="Stock" required style="flex:1;">
                </div>
                <button type="submit" class="btn-login" style="background:#6f42c1; margin-top:15px;">Guardar</button>
            </form>
        </div>

        <div id="form-modificar" class="form-section form-box" style="border-top: 5px solid #007bff;">
            <h3>Modificar Videojuego</h3>
            <form method="POST">
                <input type="hidden" name="accion" value="modificar">
                <label>ID Juego:</label>
                <input type="number" id="mod_id_juego" name="id_juego" readonly style="width:100px; background:#e9ecef; margin-bottom:10px;">
                <input type="text" id="mod_titulo" name="titulo" required style="width:100%; margin-bottom:10px;">
                <div style="display:flex; gap:10px;">
                    <input type="text" id="mod_plataforma" name="plataforma" required style="flex:1;">
                    <input type="number" step="0.01" id="mod_precio" name="precio" required style="flex:1;">
                    <input type="number" id="mod_stock" name="stock" required style="flex:1;">
                </div>
                <button type="submit" class="btn-login" style="margin-top:15px;">Actualizar</button>
            </form>
        </div>

        <div id="form-eliminar" class="form-section form-box" style="border-top: 5px solid #dc3545;">
            <h3>Eliminar Videojuego</h3>
            <form method="POST" onsubmit="return confirm('¿Eliminar?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="number" id="del_id" name="id_juego" placeholder="ID Juego" required style="width:100%; margin-bottom:15px;">
                <button type="submit" class="btn-login" style="background:#dc3545;">🗑️ Eliminar</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Título</th><th>Plataforma</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->query("EXEC sp_Videojuegos_Leer");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Convertir a JSON
                            $json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            echo "<tr>";
                            echo "<td>{$row['id_juego']}</td>";
                            echo "<td>{$row['titulo']}</td>";
                            echo "<td>{$row['plataforma']}</td>";
                            echo "<td>$" . number_format($row['precio'],2) . "</td>";
                            echo "<td>{$row['stock']}</td>";
                            echo "<td style='text-align:center;'>
                                    <button class='btn-row-edit' data-json='$json'>✏️</button>
                                    <button class='btn-row-delete' data-id='{$row['id_juego']}'>🗑️</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } catch(Exception $e) { echo "<tr><td colspan='6'>Sin datos</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="js/main.js"></script>

</body>
</html>