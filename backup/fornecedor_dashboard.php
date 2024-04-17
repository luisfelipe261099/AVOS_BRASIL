<?php
// Move a chamada para session_start() para o início do arquivo, antes de qualquer outra saída
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total de pedidos do fornecedor
    $fornecedor_id = $_SESSION['user_id'];
    $queryTotalPedidos = "SELECT COUNT(*) AS total_pedidos FROM compras WHERE fornecedor_id = :fornecedor_id";
    $stmtTotalPedidos = $pdo->prepare($queryTotalPedidos);
    $stmtTotalPedidos->bindParam(':fornecedor_id', $fornecedor_id);
    $stmtTotalPedidos->execute();
    $totalPedidos = $stmtTotalPedidos->fetch(PDO::FETCH_ASSOC);

    // Últimos pedidos do fornecedor
    $queryUltimosPedidos = "SELECT compras.*, usuarios.nome AS nome_cliente
                            FROM compras
                            JOIN usuarios ON compras.lojista_id = usuarios.id
                            WHERE compras.fornecedor_id = :fornecedor_id
                            ORDER BY compras.id DESC
                            LIMIT 5";
    $stmtUltimosPedidos = $pdo->prepare($queryUltimosPedidos);
    $stmtUltimosPedidos->bindParam(':fornecedor_id', $fornecedor_id);
    $stmtUltimosPedidos->execute();
    $ultimosPedidos = $stmtUltimosPedidos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Compras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <div class="jumbotron">
        <h1 class="display-4">Dashboard - Fornecedor</h1>
        <p class="lead">Bem-vindo ao seu painel de controle.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <canvas id="graficoPedidos"></canvas>
        </div>
        <div class="col-md-6">
            <h4>Últimos Pedidos</h4>
            <ul class="list-group">
                <?php foreach ($ultimosPedidos as $pedido): ?>
                    <li class="list-group-item">
                        <a href="informacoes_pedido.php?pedido_id=<?php echo $pedido['id']; ?>">
                            Pedido #<?php echo $pedido['id']; ?> - Cliente: <?php echo $pedido['nome_cliente']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('graficoPedidos').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total de Pedidos'],
            datasets: [{
                label: 'Número de Pedidos',
                data: [<?php echo $totalPedidos['total_pedidos']; ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<h4><br></h4>
<h4><br></h4>
<h4><br></h4>
<footer class="footer mt-auto py-3 bg-dark text-white">
    <div class="container text-center">
        <span class="text-muted">© 2024 Sua Loja - Todos os direitos reservados</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de pedidos
    var ctxPedidos = document.getElementById('pedidosChart').getContext('2d');
    var pedidosChart = new Chart(ctxPedidos, {
        type: 'bar',
        data: {
            labels: ['Pedidos'],
            datasets: [{
                label: 'Total de Pedidos',
                data: [<?php echo $totalPedidos['total_pedidos']; ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
