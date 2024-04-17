<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'fornecedor') {
    header("Location: login.php");
    exit();
}

if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Excluir produto, se solicitado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_produto'])) {
    $produto_id = $_POST['produto_id'];
    $query = "DELETE FROM produtos WHERE id = :produto_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':produto_id', $produto_id);
    $stmt->execute();
    header("Location: fornecedor_pedidos.php");
    exit();
}

// Finalizar pedido, se solicitado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_pedido'])) {
    $pedido_id = $_POST['pedido_id'];
    $query = "UPDATE compras SET status_pedido = 'Finalizado' WHERE id = :pedido_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':pedido_id', $pedido_id);
    $stmt->execute();
    header("Location: fornecedor_pedidos.php");
    exit();
}

// Atualizar valor do produto, se solicitado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_produto'])) {
    $pedido_id = $_POST['pedido_id'];
    $novo_valor = floatval($_POST['novo_valor']); // Convertendo para float
    try {
        $query = "UPDATE compras SET valor_pedido = :novo_valor WHERE id = :pedido_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':novo_valor', $novo_valor, PDO::PARAM_STR); // Definindo como string para float
        $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT); // Definindo como inteiro
        if (!$stmt->execute()) {
            echo "Erro ao executar a query: ";
            print_r($stmt->errorInfo());
            exit();
        }
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        echo "Erro ao atualizar o valor do pedido: " . $e->getMessage();
    }
}

// Recuperar os pedidos dos lojistas relacionados aos produtos do fornecedor logado
$pedidos = array();
$fornecedor_id = $_SESSION['user_id'];
$queryPedidos = "SELECT compras.*, produtos.nome AS produto_nome, produtos.preco AS produto_preco, usuarios.nome AS lojista_nome
          FROM compras
          JOIN produtos ON compras.produto_id = produtos.id
          JOIN usuarios ON compras.lojista_id = usuarios.id
          WHERE compras.fornecedor_id = :fornecedor_id";
$stmtPedidos = $pdo->prepare($queryPedidos);
$stmtPedidos->bindParam(':fornecedor_id', $fornecedor_id);
$stmtPedidos->execute();
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Compras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="fornecedor_dashboard.php">AVOS BRASIL </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
        <li class="nav-item">
                <a class="nav-link" href="cadastro_produto.php">Produtos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="fornecedor_pedidos.php">Pedidos</a>
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
        <div class="carousel-item active">
            <img src="1.png" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item">
            <img src="1.png" class="d-block w-100" alt="Slide 2">
        </div>
    </div>
</div>

<div class="container">
    <h2 class="mt-5">Dashboard - Fornecedor</h2>
    <h4 class="mt-4">Pedidos dos Lojistas</h4>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="filtro-pedidos">Filtrar pedidos dos lojistas</label>
            <input class="form-control mb-3" id="filtro-pedidos" type="text" placeholder="Pesquisar...">
        </div>
        <div class="form-group col-md-3">
            <label for="filtro-status">Filtrar por status</label>
            <select class="form-control" id="filtro-status">
                <option value="">Todos</option>
                <option value="Finalizado">Finalizado</option>
                <option value="Não finalizado">Não finalizado</option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="filtro-data">Filtrar por data</label>
            <select class="form-control" id="filtro-data">
                <option value="">Todos</option>
                <option value="Agendado">Agendado</option>
                <option value="Não agendado">Não agendado</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Lojista</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Pedido</th>
                    <th>Status Pedido</th>
                    <th>Data Programada</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pedidos)): ?>
                    <?php foreach ($pedidos as $key => $pedido): ?>
                        <tr>
                            <td><?php echo $pedido['lojista_nome']; ?></td>
                            <td><?php echo $pedido['produto_nome']; ?></td>
                            <td><?php echo $pedido['quantidade']; ?></td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                    <input type="hidden" name="produto_id" value="<?php echo $pedido['produto_id']; ?>">
                                    <div class="form-group">
                                        <input type="text" name="novo_valor" class="form-control" value="<?php echo $pedido['valor_pedido']; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary" name="atualizar_produto">Atualizar</button>
                                </form>
                            </td>
                            <td><?php echo $pedido['status_pedido']; ?></td>
                            <td>
                                <?php if ($pedido['data_programada']): ?>
                                    <?php echo date('d/m/Y', strtotime($pedido['data_programada'])); ?>
                                <?php else: ?>
                                    Não agendado
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($pedido['status_pedido'] != 'Finalizado'): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success" name="finalizar_pedido">Finalizar Pedido</button>
                                    </form>
                                <?php endif; ?>
                                <a href="informacoes_pedido.php?pedido_id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-info">Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Nenhum pedido encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
