<?php 
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'lojista') {
    header("Location: login.php");
    exit();
}

if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Recuperar o bairro do lojista
$query = "SELECT bairro FROM usuarios WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$lojista = $stmt->fetch(PDO::FETCH_ASSOC);
$lojista_bairro = $lojista['bairro'];

// Recuperar fornecedores do mesmo bairro do lojista
$query = "SELECT * FROM usuarios WHERE nivel_acesso = 'fornecedor' AND bairro = :bairro";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':bairro', $lojista_bairro);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para finalizar a compra
function finalizarCompra($pdo, $lojista_id, $fornecedor_id, $produto_id, $quantidade, $observacao, $data_programada) {
    try {
        // Inserir os detalhes da compra na tabela de compras
        $query = "INSERT INTO compras (lojista_id, fornecedor_id, produto_id, quantidade, observacao, data_programada) VALUES (:lojista_id, :fornecedor_id, :produto_id, :quantidade, :observacao, :data_programada)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':lojista_id', $lojista_id);
        $stmt->bindParam(':fornecedor_id', $fornecedor_id);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':observacao', $observacao);
        $stmt->bindParam(':data_programada', $data_programada, PDO::PARAM_STR);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        echo "Erro ao finalizar a compra: " . $e->getMessage();
        return false;
    }
}

// Verificar se o formulário foi submetido para finalizar a compra
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar_compra'])) {
    $lojista_id = $_SESSION['user_id'];
    $fornecedor_id = $_SESSION['fornecedor_id'];
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'];
    $observacao = $_POST['observacao'];
    $data_programada = isset($_POST['data_programada']) ? $_POST['data_programada'] : date('Y-m-d');

    if (finalizarCompra($pdo, $lojista_id, $fornecedor_id, $produto_id, $quantidade, $observacao, $data_programada)) {
        echo '<script>alert("Produto adicionado ao carrinho com sucesso!");</script>';
    }
}

// Recuperar produtos de um fornecedor específico (se selecionado)
$produtos = array();
$fornecedor_id = isset($_POST['fornecedor_id']) ? $_POST['fornecedor_id'] : (isset($_SESSION['fornecedor_id']) ? $_SESSION['fornecedor_id'] : '');
if (!empty($fornecedor_id)) {
    $_SESSION['fornecedor_id'] = $fornecedor_id;
    $query = "SELECT * FROM produtos WHERE fornecedor_id = :fornecedor_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fornecedor_id', $fornecedor_id);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Pesquisa por nome de produto
if(isset($_POST['search'])) {
    $search = $_POST['search'];
    $query = "SELECT * FROM produtos WHERE nome LIKE :search";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', '%' . $search . '%');
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">AVOS BRASIL</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="lojista_dashboard.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="minhas_compras.php">Meus Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lojista_compra.php">Marketplace</a>
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
    <div class="jumbotron">
        <h1 class="display-4">Bem-vindo ao Marketplace!</h1>
        <p class="lead">Aqui você pode selecionar produtos de diversos fornecedores e realizar suas compras com facilidade.</p>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h4>Filtrar por Fornecedor</h4>
                <form method="post" action="">
                    <div class="form-group">
                        <select class="form-control" name="fornecedor_id" onchange="this.form.submit()">
                            <option value="">Selecione um fornecedor</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?php echo $fornecedor['id']; ?>" <?php if($fornecedor_id == $fornecedor['id']) echo 'selected'; ?>><?php echo $fornecedor['nome']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row product-list">
                <?php if (!empty($produtos)): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="col-md-4">
                            <div class="product-card">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($produto['foto']); ?>" class="img-fluid mb-2" alt="<?php echo $produto['nome']; ?>">
                                <h5><?php echo $produto['nome']; ?></h5>
                                <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                <form method="post" action="">
                                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                                    <div class="form-group">
                                        <label for="quantidade">Quantidade:</label>
                                        <input type="number" name="quantidade" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="observacao">Observação:</label>
                                        <textarea name="observacao" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_programada">Agendar:</label>
                                        <input type="date" name="data_programada" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block" name="finalizar_compra">Adicionar ao Carrinho</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <p>Nenhum produto disponível.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
