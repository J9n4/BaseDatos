<?php
session_start();
require_once 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['usuario'];
    $pass = $_POST['clave'];

    try {
        $sql = "EXEC sp_ValidarLogin ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user, $pass]);
        
        if ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['usuario_id'] = $fila['ID_Usuario'];
            $_SESSION['usuario_nombre'] = $fila['NombreUsuario'];
            header("Location: pagina.php");
            exit();
        } else {
            $error = "❌ Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $error = "Error de sistema: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body class="login-body">

    <div class="login-box">
        <h2>Acceso Sistema</h2>
        <p>Gestión de Videojuegos</p>
        
        <?php if($error) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Contraseña" required>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
    </div>

</body>
</html>