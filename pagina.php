<?php
// pagina.php - DASHBOARD FINAL
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

// --- OBTENER ESTADÍSTICAS ---
try {
    // 1. Clientes
    $stmt = $conn->query("SELECT COUNT(*) as total FROM Clientes");
    $totalClientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Videojuegos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM Videojuegos");
    $totalJuegos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Ventas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM Transacciones");
    $totalVentas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // 4. Dinero
    $stmt = $conn->query("SELECT SUM(total) as dinero FROM Transacciones");
    $filaDinero = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalDinero = $filaDinero['dinero'] ? $filaDinero['dinero'] : 0;

    // 5. Logs (Auditoría)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM logs");
    $totalLogs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

} catch (PDOException $e) {
    $totalClientes = 0; $totalJuegos = 0; $totalVentas = 0; $totalDinero = 0; $totalLogs = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - GameStore</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🎮 GameStore Admin</a>
        <div class="user-info">
            Hola, <b><?php echo $_SESSION['usuario_nombre']; ?></b>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <h1>Panel de Control</h1>
        <p class="subtitle">Bienvenido al sistema de gestión completo.</p>

        <div class="dashboard-grid">
            
            <a href="clientes.php" class="card clientes">
                <div class="card-icon">👥</div>
                <h3>Clientes</h3>
                <div class="number"><?php echo $totalClientes; ?></div>
                <div class="cta">Gestionar &rarr;</div>
            </a>

            <a href="videojuegos.php" class="card videojuegos">
                <div class="card-icon">🕹️</div>
                <h3>Inventario</h3>
                <div class="number"><?php echo $totalJuegos; ?></div>
                <div class="cta">Ver Juegos &rarr;</div>
            </a>

            <a href="transacciones.php" class="card transacciones">
                <div class="card-icon">🛒</div>
                <h3>Ventas</h3>
                <div class="number"><?php echo $totalVentas; ?></div>
                <div class="cta">Ir a Caja &rarr;</div>
            </a>

            <a href="reportes.php" class="card reportes">
                <div class="card-icon">💰</div>
                <h3>Ingresos</h3>
                <div class="number">$<?php echo number_format($totalDinero, 0, ',', '.'); ?></div>
                <div class="cta">Ver Reportes &rarr;</div>
            </a>

            <a href="logs.php" class="card" style="border-left-color: #2d3436;">
                <div class="card-icon">🕵️</div>
                <h3>Auditoría</h3>
                <div class="number"><?php echo $totalLogs; ?></div>
                <div class="cta">Ver Logs &rarr;</div>
            </a>

        </div>

        <h3 style="margin-top: 50px;">Últimos Movimientos</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Juego</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT TOP 5 T.fecha, C.nombre, C.apellido, V.titulo, T.total 
                                FROM Transacciones T
                                JOIN Clientes C ON T.id_cliente = C.id_cliente
                                JOIN Videojuegos V ON T.id_juego = V.id_juego
                                ORDER BY T.fecha DESC";
                        $stmt = $conn->query($sql);
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $fecha = is_object($row['fecha']) ? $row['fecha']->format('Y-m-d') : $row['fecha'];
                            echo "<tr>";
                            echo "<td>".$fecha."</td>";
                            echo "<td>".$row['nombre']." ".$row['apellido']."</td>";
                            echo "<td>".$row['titulo']."</td>";
                            echo "<td>$".number_format($row['total'], 0, ',', '.')."</td>";
                            echo "</tr>";
                        }
                    } catch(Exception $e) {
                        echo "<tr><td colspan='4'>No hay datos recientes.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>