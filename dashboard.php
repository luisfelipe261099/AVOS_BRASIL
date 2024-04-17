<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nivel_acesso = $_SESSION['nivel_acesso'];
if ($nivel_acesso == 'administrador') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($nivel_acesso == 'fornecedor') {
    header("Location: fornecedor_dashboard.php");
    exit();
} elseif ($nivel_acesso == 'lojista') {
    header("Location: lojista_dashboard.php");
    exit();
}
?>
