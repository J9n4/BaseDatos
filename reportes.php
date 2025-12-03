<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

$resultadoEscalar = null;
$resultadosTabla = [];
$clienteSeleccionado = "";


try {
    $stmt = $conn->query("SELECT id_cliente, nombre, apellido FROM Clientes ORDER BY nombre");
    $listaClientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT DISTINCT plataforma FROM Videojuegos");
    $listaPlataformas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { 
    $listaClientes = []; $listaPlataformas = [];
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //  FUNCIÓN ESCALAR
    if (isset($_POST['btn_escalar'])) {
        $id = $_POST['id_cliente_reporte'];
        $clienteSeleccionado = $_POST['nombre_cliente_hidden']; 
        try {
            
            $sql = "SELECT dbo.fn_TotalGastadoCliente(?) as TotalGastado";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            $resultadoEscalar = $fila['TotalGastado'];
        } catch (Exception $e) { $resultadoEscalar = "Error"; }
    }

    //  FUNCIÓN TABLA 
    if (isset($_POST['btn_tabla'])) {
        $plat = $_POST['plataforma_reporte'];
        try {
            
            $sql = "SELECT * FROM dbo.fn_JuegosPorPlataforma(?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$plat]);
            $resultadosTabla = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { $resultadosTabla = []; }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <div class="navbar">
        <a href="pagina.php" class="logo">🏠 Inicio</a>
        <div class="user-info">Reportes del Sistema 📊</div>
    </div>

    <div class="container">
        
        <h1>Consultas Especiales</h1>
        <p class="subtitle">Reportes generados mediante Funciones SQL.</p>

        <div class="report-section border-orange">
            <h3 class="report-title text-orange">💰 Total Gastado (Función Escalar)</h3>
            <p>Calcula el dinero total histórico gastado por un cliente.</p>
            
            <form method="POST" class="search-bar">
                <select name="id_cliente_reporte" id="select_cliente" required>
                    <option value="">-- Seleccione Cliente --</option>
                    <?php foreach($listaClientes as $c): ?>
                        <option value="<?php echo $c['id_cliente']; ?>" data-name="<?php echo $c['nombre'].' '.$c['apellido']; ?>">
                            <?php echo $c['nombre'] . " " . $c['apellido']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="nombre_cliente_hidden" id="nombre_hidden">
                
                <button type="submit" name="btn_escalar" class="btn-report bg-orange">Calcular</button>
            </form>

            <?php if ($resultadoEscalar !== null): ?>
                <div class="result-box">
                    <strong>👤 Cliente:</strong> <?php echo $clienteSeleccionado; ?><br>
                    <strong>💵 Total Gastado:</strong> $<?php echo number_format((float)$resultadoEscalar, 0, ',', '.'); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="report-section border-purple">
            <h3 class="report-title text-purple">🕹️ Catálogo por Plataforma (Función Tabla)</h3>
            <p>Muestra todos los juegos disponibles para una consola específica.</p>
            
            <form method="POST" class="search-bar">
                <select name="plataforma_reporte" required>
                    <option value="">-- Seleccione Plataforma --</option>
                    <?php foreach($listaPlataformas as $p): ?>
                        <option value="<?php echo $p['plataforma']; ?>">
                            <?php echo $p['plataforma']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" name="btn_tabla" class="btn-report bg-purple">Buscar Juegos</button>
            </form>

            <?php if (!empty($resultadosTabla)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Precio</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resultadosTabla as $row): ?>
                                <tr>
                                    <td><?php echo $row['id_juego']; ?></td>
                                    <td><b><?php echo $row['titulo']; ?></b></td>
                                    <td>$<?php echo number_format($row['precio'], 0, ',', '.'); ?></td>
                                    <td><?php echo $row['stock']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (isset($_POST['btn_tabla'])): ?>
                <div class="msg-box msg-error" style="margin-top:20px;">
                    No se encontraron resultados.
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script>
        const select = document.getElementById('select_cliente');
        if(select){
            select.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                document.getElementById('nombre_hidden').value = selectedOption.getAttribute('data-name');
            });
        }
    </script>
    
    </body>
</html>