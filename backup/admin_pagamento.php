<?php
session_start();
require_once 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Verificar se o ID do usuário foi enviado por meio do parâmetro GET
if (isset($_GET['id'])) {
    $usuario_id = $_GET['id'];

    // Verificar se a opção de ativar foi selecionada
    if (isset($_GET['ativar'])) {
        $sql = "UPDATE `usuarios` SET `pagamento_confirmado` = 1 WHERE `id` = :usuario_id";
        $message = "Status de pagamento ativado com sucesso.";
    }

    // Verificar se a opção de desativar foi selecionada
    if (isset($_GET['desativar'])) {
        $sql = "UPDATE `usuarios` SET `pagamento_confirmado` = 0 WHERE `id` = :usuario_id";
        $message = "Status de pagamento desativado com sucesso.";
    }

    // Executar a consulta SQL
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();

    // Redirecionar de volta para a página de administração
    header("Location: admin_pagamento.php");
    exit();
}

// Obter todos os usuários do banco de dados
$query = "SELECT * FROM usuarios";
$stmt = $pdo->query($query);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Administração de Pagamentos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    </style>
</head>
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
    <h2 class="text-center mb-4">Administração de Pagamentos</h2>
    <?php if(isset($message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Status de Pagamento</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['nome']; ?></td>
                        <td><?php echo $usuario['email']; ?></td>
                        <td><?php echo $usuario['pagamento_confirmado'] == 1 ? 'Ativo' : 'Inativo'; ?></td>
                        <td>
                            <a href="?id=<?php echo $usuario['id']; ?>&ativar" class="btn btn-success btn-sm">Ativar</a>
                            <a href="?id=<?php echo $usuario['id']; ?>&desativar" class="btn btn-danger btn-sm">Desativar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2><br></h2>
        <h2><br></h2>
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
