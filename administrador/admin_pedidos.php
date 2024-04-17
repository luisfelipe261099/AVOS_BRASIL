<?php
session_start();
require_once 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Consulta para recuperar as compras com o nome do produto, fornecedor e lojista
$queryCompras = "SELECT c.id, p.nome AS produto_nome, u_f.nome AS fornecedor_nome, u_l.nome AS lojista_nome, c.status_pedido 
                 FROM compras c 
                 INNER JOIN produtos p ON c.produto_id = p.id 
                 INNER JOIN usuarios u_f ON p.fornecedor_id = u_f.id
                 INNER JOIN usuarios u_l ON c.lojista_id = u_l.id";
$stmtCompras = $pdo->query($queryCompras);
$compras = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);

// Filtro de busca por nome do produto, fornecedor ou lojista
if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $busca = $_GET['busca'];
    $queryCompras .= " WHERE p.nome LIKE :busca OR u_f.nome LIKE :busca OR u_l.nome LIKE :busca";
    $stmtCompras = $pdo->prepare($queryCompras);
    $stmtCompras->execute([':busca' => '%' . $busca . '%']);
    $compras = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Compras</title>
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
    <a class="navbar-brand" href="#">AVOS BRASIL </a>
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
    <h2>Pedidos</h2>
    <!-- Campo de busca -->
    <form class="form-inline mb-3" method="GET" action="">
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Buscar por nome do produto, fornecedor ou lojista" name="busca">
        </div>
        <button type="submit" class="btn btn-primary ml-2">Buscar</button>
    </form>

    <!-- Tabela de compras -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Produto</th>
                    <th class="text-center">Fornecedor</th>
                    <th class="text-center">Lojista</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras as $compra): ?>
                    <tr>
                        <td class="text-center"><?php echo $compra['produto_nome']; ?></td>
                        <td class="text-center"><?php echo $compra['fornecedor_nome']; ?></td>
                        <td class="text-center"><?php echo $compra['lojista_nome']; ?></td>
                        <td class="text-center"><?php echo $compra['status_pedido']; ?></td>
                        <td class="text-center">
                            <?php if ($compra['status_pedido'] == 'pendente'): ?>
                                <a href="?confirmar_compra=<?php echo $compra['id']; ?>" class="btn btn-sm btn-success">Confirmar</a>
                                <!-- Formulário para excluir pedido -->
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="d-inline">
                                    <input type="hidden" name="compra_id" value="<?php echo $compra['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" name="excluir_pedido">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2><br></h2>
        <h2><br></h2>
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
