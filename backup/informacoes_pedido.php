<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'fornecedor') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['pedido_id'])) {
    $pedido_id = $_GET['pedido_id'];

    // Recuperar informações do pedido
    $query = "SELECT compras.*, produtos.nome AS produto_nome, produtos.preco AS produto_preco, usuarios.nome AS lojista_nome, usuarios.email AS lojista_email
              FROM compras
              JOIN produtos ON compras.produto_id = produtos.id
              JOIN usuarios ON compras.lojista_id = usuarios.id
              WHERE compras.id = :pedido_id AND compras.fornecedor_id = :fornecedor_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':pedido_id', $pedido_id);
    $stmt->bindParam(':fornecedor_id', $_SESSION['user_id']);
    $stmt->execute();
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o fornecedor é o proprietário do pedido
    if (!$pedido) {
        echo "Pedido não encontrado ou você não tem permissão para visualizá-lo.";
        exit();
    }
} else {
    echo "ID do pedido não fornecido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Informações do Pedido</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<style>
        /* Estilos adicionais para a tabela de pedidos */
        .table-scrollable {
            max-height: 400px; /* Altura máxima da tabela com rolagem */
            overflow-y: auto; /* Adiciona rolagem vertical */
        }
        body {
            color: black; /* Altera a cor do texto para preto */
            background-image: url('https://img.freepik.com/vetores-gratis/vetor-de-fundo-de-padrao-geometrico-branco-e-cinza_53876-136510.jpg?size=626&ext=jpg&ga=GA1.1.735520172.1712102400&semt=sph');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            opacity: 0.9; /* Ajuste a opacidade conforme necessário */
            filter: alpha(opacity=50); /* Para navegadores antigos */
        }
    </style>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="fornecedor_dashboard.php">AVOS BRASIL </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
        <li class="nav-item">
                <a class="nav-link" href="fornecedor_pedidos.php">Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cadastro_produto.php">Cadastro Produtos</a>
            </li>
        </ul>
       
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        <ul class="navbar-nav ml-auto">
        <li class="nav-item">
                <a class="nav-link" href="lojista_perfil.php">Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?logout=true">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<body>
    <div class="container">
        <h2 class="mt-5">Informações do Pedido</h2>
        <div class="card mt-4">
            <div class="card-header">
                Detalhes do Pedido
            </div>
            <div class="card-body">
                <p><strong>ID do Pedido:</strong> <?php echo $pedido['id']; ?></p>
                <p><strong>Produto:</strong> <?php echo $pedido['produto_nome']; ?></p>
                <p><strong>Preço Unitário:</strong> R$ <?php echo number_format($pedido['produto_preco'], 2, ',', '.'); ?></p>
                <p><strong>Quantidade:</strong> <?php echo $pedido['quantidade']; ?></p>
                <p><strong>Valor Total:</strong> R$ <?php echo number_format($pedido['valor_pedido'], 2, ',', '.'); ?></p>
                <p><strong>Observação:</strong> <?php echo $pedido['observacao'] ?? 'N/A'; ?></p>
                <hr>
                <h5 class="card-title">Informações do Lojista</h5>
                <p><strong>Nome:</strong> <?php echo $pedido['lojista_nome']; ?></p>
                <p><strong>E-mail:</strong> <?php echo $pedido['lojista_email']; ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="fornecedor_pedidos.php" class="btn btn-primary">Voltar</a>
            <button onclick="window.print();" class="btn btn-secondary">Imprimir</button>
            <a href="mailto:<?php echo $pedido['lojista_email']; ?>" class="btn btn-info">Enviar por E-mail</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
