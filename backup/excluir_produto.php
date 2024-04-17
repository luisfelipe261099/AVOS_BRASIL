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
    // Redirecionar de volta para a página do dashboard após excluir o produto
    header("Location: excluir_produto.php");
    exit();
}

// Recuperar os produtos cadastrados pelo fornecedor logado
$produtosFornecedor = array(); // Inicializa a variável como um array vazio
$fornecedor_id = $_SESSION['user_id'];
$queryProdutos = "SELECT * FROM produtos WHERE fornecedor_id = :fornecedor_id";
$stmtProdutos = $pdo->prepare($queryProdutos);
$stmtProdutos->bindParam(':fornecedor_id', $fornecedor_id);
$stmtProdutos->execute();
$produtosFornecedor = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Fornecedor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
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
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Administrador</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="fornecedor_dashboard.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastro_produto.php">Cadastro Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="excluir_produto.php">Produtos</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="?logout=true">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h2 class="mt-5">Dashboard - Fornecedor</h2>
       
        <h4 class="mt-4">Produtos Cadastrados</h4>
        <input class="form-control mb-3" id="filtro-produtos" type="text" placeholder="Filtrar produtos cadastrados...">
        <label for="quantidade-produtos">Quantidade de Produtos:</label>
        <select class="form-control mb-3" id="quantidade-produtos">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
        <ul class="list-group" id="lista-produtos">
            <?php if (!empty($produtosFornecedor)): ?>
                <?php foreach ($produtosFornecedor as $produto): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo $produto['nome']; ?>
                        <form method="post" action="">
                            <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" name="excluir_produto">Excluir</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item">Nenhum produto cadastrado.</li>
            <?php endif; ?>
        </ul>
       
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function(){
            // Filtro para produtos
            $("#filtro-produtos").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#lista-produtos li").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Alterar quantidade de produtos exibidos
            $("#quantidade-produtos").on("change", function() {
                var quantidade = $(this).val();
                // Atualizar exibição de produtos
            });
        });
    </script>
</body>
</html>
