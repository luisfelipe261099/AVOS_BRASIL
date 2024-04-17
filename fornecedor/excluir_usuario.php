<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])) {
    $usuario_id = $_GET['id'];

    $query = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();

    header("Location: dashboard_admin.php");
    exit();
} else {
    header("Location: dashboard_admin.php");
    exit();
}
?>
