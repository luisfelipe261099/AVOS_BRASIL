<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Consulta para recuperar os últimos acessos dos usuários
$queryUltimosAcessos = "SELECT nome, ultimo_acesso FROM usuarios ORDER BY ultimo_acesso DESC";
$stmtUltimosAcessos = $pdo->query($queryUltimosAcessos);
$ultimosAcessos = $stmtUltimosAcessos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Últimos Acessos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="admin_dashboard.php">AVOS BRASIL</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="admin_produto.php">Produtos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_pedidos.php">Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_usuario.php">Administrar Usuários</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_acesso.php">Acessos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_pagamento.php">Liberação</a>
            </li>
        </ul>
       
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="lojista_perfil.php">Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Adicionando o slideshow -->
<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <!-- Adicione aqui seus slides -->
        <div class="carousel-item active">
            <img src="1.png" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item">
            <img src="1.png" class="d-block w-100" alt="Slide 2">
        </div>
        <!-- Adicione mais slides conforme necessário -->
    </div>
</div>


<div class="container mt-5">
    <h2>Últimos Acessos dos Usuários</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="tabelaAcessos">
            <thead>
            <tr>
                <th>Nome</th>
                <th>Último Acesso</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ultimosAcessos as $acesso): ?>
                <tr>
                    <td><?php echo $acesso['nome']; ?></td>
                    <td><?php echo $acesso['ultimo_acesso']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Adicionando footer padrão -->
<footer class="footer mt-auto py-3 bg-dark text-white">
    <div class="container text-center">
        <span class="text-muted">© 2024 Sua Loja - Todos os direitos reservados</span>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
