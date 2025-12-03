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

// --- PRE-CARGA DE DATOS (Selects) ---
$listaClientes = [];
$listaJuegos = [];

try {
    $stmtC = $conn->query("SELECT id_cliente, nombre, apellido FROM Clientes ORDER BY nombre");
    $listaClientes = $stmtC->fetchAll(PDO::FETCH_ASSOC);

    $stmtV = $conn->query("SELECT id_juego, titulo, precio, stock FROM Videojuegos ORDER BY titulo");
    $listaJuegos = $stmtV->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { }

// --- LÓGICA CRUD ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if ($_POST['accion'] == 'agregar') {
            $sql = "EXEC sp_Transacciones_Crear ?, ?, ?";
            $conn->prepare($sql)->execute([$_POST['id_cliente'], $_POST['id_juego'], $_POST['cantidad']]);
            $mensaje = "✅ Venta registrada."; $tipo_mensaje = "success";
        }
        elseif ($_POST['accion'] == 'modificar') {
            $sql = "EXEC sp_Transacciones_Actualizar ?, ?, ?, ?";
            $conn->prepare($sql)->execute([$_POST['id_transaccion'], $_POST['id_cliente'], $_POST['id_juego'], $_POST['cantidad']]);
            $mensaje = "✅ Venta actualizada."; $tipo_mensaje = "success";
        }
        elseif ($_POST['accion'] == 'eliminar') {
            $sql = "EXEC sp_Transacciones_Eliminar @id_transaccion = ?";
            $conn->prepare($sql)->execute([$_POST['id_transaccion']]);
            $mensaje = "🗑️ Venta anulada."; $tipo_mensaje = "success";
        }
    } catch (PDOException $e) {
        $errorRaw = $e->getMessage();
        $mensaje = "❌ Error: " . str_replace("SQLSTATE[42000]: [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]", "", $errorRaw);
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ventas</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🏠 Inicio</a>
        <div class="user-info">Caja y Ventas 🛒</div>
    </div>

    <div class="container">
        
        <?php if($mensaje): ?>
            <div class="msg-box <?php echo ($tipo_mensaje == 'success') ? 'msg-success' : 'msg-error'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <button class="btn-action btn-add" data-target="form-agregar" style="background-color: #28a745;">➕ Nueva Venta</button>
            <button class="btn-action btn-edit" data-target="form-modificar">✏️ Modificar Venta</button>
            <button class="btn-action btn-del" data-target="form-eliminar">🗑️ Anular Venta</button>
        </div>

        <div id="form-agregar" class="form-section form-box" style="border-top: 5px solid #28a745;">
            <h3>Registrar Nueva Venta</h3>
            <form method="POST">
                <input type="hidden" name="accion" value="agregar">
                <div style="display:flex; gap:15px; margin-bottom:15px;">
                    <div style="flex:1;">
                        <label>Cliente:</label>
                        <select name="id_cliente" required style="width:100%; padding:10px;">
                            <option value="">-- Seleccione --</option>
                            <?php foreach($listaClientes as $c): ?>
                                <option value="<?php echo $c['id_cliente']; ?>"><?php echo $c['nombre']." ".$c['apellido']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label>Juego:</label>
                        <select name="id_juego" required style="width:100%; padding:10px;">
                            <option value="">-- Seleccione --</option>
                            <?php foreach($listaJuegos as $j): ?>
                                <option value="<?php echo $j['id_juego']; ?>">
                                    <?php echo $j['titulo'] . " ($" . number_format($j['precio'],0) . ") [Stock:" . $j['stock'] . "]"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <label>Cantidad:</label>
                <input type="number" name="cantidad" value="1" min="1" required style="width:100px; padding:8px;">
                <button type="submit" class="btn-login" style="background:#28a745; margin-top:15px;">Confirmar</button>
            </form>
        </div>

        <div id="form-modificar" class="form-section form-box" style="border-top: 5px solid #007bff;">
            <h3>Modificar Venta</h3>
            <form method="POST">
                <input type="hidden" name="accion" value="modificar">
                <label>ID:</label>
                <input type="number" id="mod_id_transaccion" name="id_transaccion" readonly style="width:100px; background:#e9ecef; margin-bottom:10px;">
                
                <div style="display:flex; gap:15px; margin-bottom:15px;">
                    <div style="flex:1;">
                        <label>Cliente:</label>
                        <select id="mod_id_cliente" name="id_cliente" required style="width:100%; padding:10px;">
                            <?php foreach($listaClientes as $c): ?>
                                <option value="<?php echo $c['id_cliente']; ?>"><?php echo $c['nombre']." ".$c['apellido']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label>Juego:</label>
                        <select id="mod_id_juego" name="id_juego" required style="width:100%; padding:10px;">
                            <?php foreach($listaJuegos as $j): ?>
                                <option value="<?php echo $j['id_juego']; ?>"><?php echo $j['titulo']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <label>Cantidad:</label>
                <input type="number" id="mod_cantidad" name="cantidad" min="1" required style="width:100px; padding:8px;">
                <button type="submit" class="btn-login" style="margin-top:15px;">Actualizar</button>
            </form>
        </div>

        <div id="form-eliminar" class="form-section form-box" style="border-top: 5px solid #dc3545;">
            <h3>Anular Venta</h3>
            <form method="POST" onsubmit="return confirm('¿Anular venta?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="number" id="del_id" name="id_transaccion" placeholder="ID Venta" required style="width:100%; margin-bottom:15px;">
                <button type="submit" class="btn-login" style="background:#dc3545;">🗑️ Anular</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Fecha</th><th>Cliente</th><th>Juego</th><th>Cant.</th><th>Total</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->query("EXEC sp_Transacciones_Leer");
                        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $json = htmlspecialchars(json_encode($fila), ENT_QUOTES, 'UTF-8');
                            $fecha = is_object($fila['fecha']) ? $fila['fecha']->format('Y-m-d') : $fila['fecha'];
                            echo "<tr>";
                            echo "<td>{$fila['id_transaccion']}</td>";
                            echo "<td>$fecha</td>";
                            echo "<td>{$fila['Cliente']}</td>";
                            echo "<td>{$fila['Videojuego']}</td>";
                            echo "<td style='text-align:center;'>{$fila['cantidad']}</td>";
                            echo "<td>$" . number_format($fila['total'], 0, ',', '.') . "</td>";
                            echo "<td style='text-align:center;'>
                                    <button class='btn-row-edit' data-json='$json'>✏️</button>
                                    <button class='btn-row-delete' data-id='{$fila['id_transaccion']}'>🗑️</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } catch (Exception $e) { echo "<tr><td colspan='7'>Sin registros.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="js/main.js"></script>

</body>
</html>