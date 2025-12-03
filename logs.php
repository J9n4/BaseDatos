<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

// Opción para limpiar el historial
if (isset($_POST['limpiar'])) {
    try {
        $conn->query("TRUNCATE TABLE logs");
    } catch (Exception $e) { }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Logs de Auditoría</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🏠 Inicio</a>
        <div class="user-info">Sistema de Auditoría (Trigger) 🕵️</div>
    </div>

    <div class="container">
        <h1>Registro de Actividad</h1>
        <p class="subtitle">Historial generado automáticamente por el Trigger <b>trigger_auditoria</b>.</p>

        <div class="log-container">
            <?php
            try {
                $sql = "SELECT * FROM logs ORDER BY fecha DESC";
                $stmt = $conn->query($sql);
                
                $hayDatos = false;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $hayDatos = true;
                    // Formato de fecha
                    $fecha = is_object($row['fecha']) ? $row['fecha']->format('Y-m-d H:i:s') : $row['fecha'];
                    
                    echo "<div class='log-entry'>";
                    echo "<span class='log-date'>[$fecha]</span>";
                    echo "<span class='log-action'>{$row['accion']}</span>";
                    echo "<span>{$row['detalle']}</span>";
                    echo "</div>";
                }

                if (!$hayDatos) {
                    echo "<div style='padding:20px; text-align:center; color:#b2bec3;'>--- No hay actividad registrada ---</div>";
                }

            } catch (Exception $e) {
                echo "<p>Error al cargar logs: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <form method="POST" style="margin-top: 20px; text-align: right;">
            <button type="submit" name="limpiar" class="btn-action btn-del" onclick="return confirm('¿Borrar todo el historial?');">
                🗑️ Limpiar Logs
            </button>
        </form>

    </div>

</body>
</html>