<?php
session_start();
require_once 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Consulta para recuperar os produtos com o nome do fornecedor
$queryProdutos = "SELECT p.id, p.nome AS produto_nome, u.nome AS fornecedor_nome 
                  FROM produtos p 
                  INNER JOIN usuarios u ON p.fornecedor_id = u.id";
$stmtProdutos = $pdo->query($queryProdutos);
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

// Filtro de busca por nome
if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $busca = $_GET['busca'];
    $queryProdutos .= " WHERE p.nome LIKE :busca OR u.nome LIKE :busca";
    $stmtProdutos = $pdo->prepare($queryProdutos);
    $stmtProdutos->execute([':busca' => '%' . $busca . '%']);
    $produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Produtos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    body {
        background-image: url('https://img.freepik.com/vetores-gratis/vetor-de-fundo-de-padrao-geometrico-branco-e-cinza_53876-136510.jpg?size=626&ext=jpg&ga=GA1.1.735520172.1712102400&semt=sph');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }

    .carousel-item img {
        height: auto;
        max-width: 100%;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="admin_dashboard.php">AVOS BRASIL </a>
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
    <h2 class="text-center">Dashboard - Produtos</h2>
    <form class="form-inline my-2 my-lg-0" method="GET" action="">
            <input class="form-control mr-sm-2" type="search" placeholder="Buscar por nome" aria-label="Search" name="busca">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
        </form>
    <!-- Tabela de produtos -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Nome do Produto</th>
                    <th class="text-center">Nome do Fornecedor</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td class="text-center"><?php echo $produto['produto_nome']; ?></td>
                        <td class="text-center"><?php echo $produto['fornecedor_nome']; ?></td>
                        <td class="text-center">
                            <a href="?excluir=<?php echo $produto['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            
        </table>
        <h2 class="text-center"><br></h2>
        <h2 class="text-center"><br></h2>
        <h2 class="text-center"><br></h2>



    </div>
    <h2><br></h2>
    <h2><br></h2>
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
