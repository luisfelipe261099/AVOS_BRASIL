<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Recuperar quantidade de usuários cadastrados
$queryUsuarios = "SELECT COUNT(*) AS total_usuarios FROM usuarios";
$stmtUsuarios = $pdo->query($queryUsuarios);
$totalUsuarios = $stmtUsuarios->fetch(PDO::FETCH_ASSOC);

// Recuperar quantidade de pedidos cadastrados
$queryPedidos = "SELECT COUNT(*) AS total_pedidos FROM compras";
$stmtPedidos = $pdo->query($queryPedidos);
$totalPedidos = $stmtPedidos->fetch(PDO::FETCH_ASSOC);

// Consulta para recuperar os últimos acessos dos usuários
$queryUltimosAcessos = "SELECT nome, ultimo_acesso FROM usuarios ORDER BY ultimo_acesso DESC LIMIT 5";
$stmtUltimosAcessos = $pdo->query($queryUltimosAcessos);
$ultimosAcessosData = array();
while ($row = $stmtUltimosAcessos->fetch(PDO::FETCH_ASSOC)) {
    $ultimosAcessosData[$row['nome']] = $row['ultimo_acesso'];
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
    <h2>Dashboard - Home</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Usuários</h5>
                    <canvas id="usuariosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Pedidos</h5>
                    <canvas id="pedidosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Últimos Acessos dos Usuários</h5>
                    <canvas id="ultimosAcessosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-3 bg-dark text-white">
    <div class="container text-center">
        <span class="text-muted">© 2024 Sua Loja - Todos os direitos reservados</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de usuários
    var ctxUsuarios = document.getElementById('usuariosChart').getContext('2d');
    var usuariosChart = new Chart(ctxUsuarios, {
        type: 'bar',
        data: {
            labels: ['Usuários'],
            datasets: [{
                label: 'Total de Usuários',
                data: [<?php echo $totalUsuarios['total_usuarios']; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
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

    // Gráfico de últimos acessos dos usuários
    var ctxUltimosAcessos = document.getElementById('ultimosAcessosChart').getContext('2d');
    var ultimosAcessosChart = new Chart(ctxUltimosAcessos, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($ultimosAcessosData)); ?>,
            datasets: [{
                label: 'Último Acesso',
                data: <?php echo json_encode(array_values($ultimosAcessosData)); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
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
</body>
</html>
