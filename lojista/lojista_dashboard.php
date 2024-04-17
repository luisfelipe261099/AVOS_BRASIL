<?php
session_start();
require_once 'db.php';

// Verificação de sessão e permissões
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'lojista') {
    header("Location: login.php");
    exit();
}

// Consulta para recuperar os promocao em promoção
$query = "SELECT * FROM promocao";
$stmt = $pdo->query($query);
if (!$stmt) {
    die('Erro na consulta ao banco de dados.');
}
$promocao = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$promocao) {
    die('Nenhum produto encontrado.');
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Lojista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .carousel-item img {
            height: 40%; /* Reduzindo a altura em 40% */
        }
        /* Estilos CSS existentes permanecem aqui */
        .product-card {
            text-align: center;
            margin-bottom: 20px;
        }
        .product-card img {
            max-width: 200px; /* Definindo o tamanho máximo da imagem */
            max-height: 200px; /* Definindo o tamanho máximo da imagem */
        }
    </style>
    
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
                <a class="nav-link" href="minhas_compras.php">Meus Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lojista_compra.php">Marktplace</a>
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
                <a class="nav-link" href="login.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <!-- Conteúdo da barra de navegação -->
</nav>

<!-- Adicionando o slideshow -->
<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <!-- Adicione aqui seus slides -->
        <div class="carousel-item active">
            <img src="https://www.friboi.com.br/_next/image/?url=https%3A%2F%2Ffuture-brand-frib.s3.amazonaws.com%2F1953_friboi_simplismente_excepcional_hero_desktop_0bbcd07507.png&w=1440&q=85" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item">
            <img src="https://www.friboi.com.br/_next/image/?url=https%3A%2F%2Ffuture-brand-frib.s3.amazonaws.com%2F1953_friboi_simplismente_excepcional_hero_desktop_0bbcd07507.png&w=1440&q=85" class="d-block w-100" alt="Slide 2">
        </div>
        <!-- Adicione mais slides conforme necessário -->
    </div>
</div>

<div class="container mt-5">
    <!-- Adicionando o título centralizado para promocao em promoção -->
    <div class="jumbotron text-center">
        <h1 class="display-4">Produtos em Promoção</h1>
        <p class="lead">Confira os principais produtos em promoção disponíveis em nossa loja.</p>
    </div>

    <!-- Exibindo os promocao em promoção -->
    <div class="row">
        <?php foreach ($promocao as $produto): ?>
            <div class="col-md-4">
                <div class="product-card">
                    <!-- Exibindo a imagem do produto -->
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($produto['foto']); ?>" class="img-fluid mb-2" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    <h5><?php echo htmlspecialchars($produto['nome']); ?></h5>
                    <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <!-- Adicione outros detalhes do produto conforme necessário -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Adicionando a segunda seção de promocao organizados por marca -->
<div class="container mt-5">
    <div class="jumbotron text-center">
        <h1 class="display-4">Principais Fornecedores</h1>
        <p class="lead">.</p>
  
    
    <div class="row">
        <!-- Card para a marca Friboi -->
        <div class="col-md-4">
            <div class="product-card">
                <img src="https://yt3.googleusercontent.com/r5L-8VJ31DpB2EPASLHlMTvduD2pyoYOs8Q0BGUUHyFq7tqKWYfsdQRiO10n_OV9WBXWx9rp=s900-c-k-c0x00ffffff-no-rj" class="img-fluid mb-2" alt="Friboi">
                <h5></h5>
                <!-- Adicione outros detalhes conforme necessário -->
            </div>
        </div>
        
        <!-- Card para a marca Copacol -->
        <div class="col-md-4">
            <div class="product-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbvsNIwxBtVzPbaDjoiGOjhgSKBhqN0oeXyalU9Ezvew&s" class="img-fluid mb-2" alt="Copacol">
                <h5></h5>
                <!-- Adicione outros detalhes conforme necessário -->
            </div>
        </div>
        
        <!-- Card para a marca Alegra -->
        <div class="col-md-4">
            <div class="product-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQRH0dYNkFS_Z6_t9o8Ado1dwyUnny6SUtKHdctDHU0lQ&s" class="img-fluid mb-2" alt="Alegra">
                <h5></h5>
                <!-- Adicione outros detalhes conforme necessário -->
            </div>
        </div>
    </div>
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
