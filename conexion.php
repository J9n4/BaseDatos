<?php

$nombreServidor = "J9N4"; 
$nombreBaseDatos = "Certamen2"; 
$usuario = "sa"; 
$password = "1234"; 

try {
    $conn = new PDO("sqlsrv:Server=$nombreServidor;Database=$nombreBaseDatos", $usuario, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    


} catch (PDOException $e) {

    die("Error crítico de conexión: " . $e->getMessage());
}
?>