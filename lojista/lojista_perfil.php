<?php
session_start();
require_once 'db.php';



$user_id = $_SESSION['user_id'];

// Obter os dados do usuário do banco de dados
$query = "SELECT * FROM usuarios WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber os novos dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cnpj = $_POST['cnpj'];
    $ramo_atuacao = $_POST['ramo_atuacao'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $senha = $_POST['senha'];

    // Atualizar os dados do usuário no banco de dados
    $sql = "UPDATE `usuarios` SET `nome` = :nome, `email` = :email, `cnpj` = :cnpj, `ramo_atuacao` = :ramo_atuacao, `endereco` = :endereco, `telefone` = :telefone, `senha` = :senha WHERE `id` = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->bindParam(':ramo_atuacao', $ramo_atuacao);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    // Redirecionar para a página do painel do lojista
    header("Location: lojista_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
 <!-- Navbar -->
 
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">AVOS BRASIL </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
        <li class="nav-item">
                <a class="nav-link" href="lojista_dashboard.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="minhas_compras.php">Pedido</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lojista_compra.php">Cotação</a>
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


    <div class="container mt-5">
        <h2>Perfil do Usuário</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $user['nome']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>">
            </div>
            <div class="form-group">
                <label for="cnpj">CNPJ:</label>
                <input type="text" class="form-control" id="cnpj" name="cnpj" value="<?php echo $user['cnpj']; ?>">
            </div>
            <div class="form-group">
                <label for="ramo_atuacao">Ramo de Atuação:</label>
                <input type="text" class="form-control" id="ramo_atuacao" name="ramo_atuacao" value="<?php echo $user['ramo_atuacao']; ?>">
            </div>
            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo $user['endereco']; ?>">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $user['telefone']; ?>">
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" value="<?php echo $user['senha']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
        </form>
        
    </div>

</body>

</html>
