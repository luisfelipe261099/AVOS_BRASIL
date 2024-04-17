<?php
session_start();
require_once 'db.php';

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Definir a variável $nivel_acesso como vazio por padrão
$nivel_acesso = '';

// Verificar se o formulário foi submetido e atribuir o valor do campo 'nivel_acesso' à variável $nivel_acesso
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $nivel_acesso = $_POST['nivel_acesso'];
}

// Construir a consulta SQL baseada no valor do filtro de nível de acesso
if (!empty($nivel_acesso)) {
    $query = "SELECT * FROM usuarios WHERE nivel_acesso LIKE '%$nivel_acesso%'";
} else {
    $query = "SELECT * FROM usuarios";
}

// Busca de usuários
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $busca = $_POST['busca'];
    // Adicionar a condição de busca ao final da consulta SQL
    $query .= " AND (nome LIKE '%$busca%' OR email LIKE '%$busca%')";
}

$stmt = $pdo->query($query);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h2>Dashboard - Administrador</h2>
        <!-- Formulário para filtro e busca de usuários -->
        <form method="post" class="mt-3 mb-3">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="nivel_acesso">Nível de Acesso:</label>
                    <select name="nivel_acesso" class="form-control">
                        <option value="">Todos</option>
                        <option value="administrador" <?php echo ($nivel_acesso == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="fornecedor" <?php echo ($nivel_acesso == 'fornecedor') ? 'selected' : ''; ?>>Fornecedor</option>
                        <option value="lojista" <?php echo ($nivel_acesso == 'lojista') ? 'selected' : ''; ?>>Lojista</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="busca">Buscar por nome ou email:</label>
                    <input type="text" name="busca" class="form-control" placeholder="Digite o nome ou email">
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>
        
        <!-- Tabela de usuários -->
        <table id="usuarios-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                   
                    <th>Nome</th>
                    <th>Nível de Acesso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $key => $usuario): ?>
                    <tr>
                        
                        <td><?php echo $usuario['nome']; ?></td>
                        <td><?php echo $usuario['nivel_acesso']; ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                            <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger">Excluir</a>
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
