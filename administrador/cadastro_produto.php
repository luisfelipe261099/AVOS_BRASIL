<?php
session_start();
require_once 'db.php';

$message = "";

// Verificar se o usuário está logado como fornecedor
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'fornecedor') {
    header("Location: login.php");
    exit();
}

try {
    // Processar o formulário de cadastro de produto quando enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recuperar os dados do formulário
        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        $marca = $_POST['marca'];
        $validade = $_POST['validade'];
        $tipo_embalagem = $_POST['tipo_embalagem'];
        $peso_kg = $_POST['peso_kg'];
        $descricao = $_POST['descricao'];
        $fornecedor_id = $_SESSION['user_id'];

        // Upload da foto
        if ($_FILES['foto']['name']) {
            $foto_temp = $_FILES['foto']['tmp_name'];
            $foto_content = file_get_contents($foto_temp);

            // Preparar e executar a consulta SQL para inserir o produto no banco de dados
            $query = "INSERT INTO produtos (nome, preco, marca, validade, tipo_embalagem, peso_kg, descricao, foto, fornecedor_id) 
                      VALUES (:nome, :preco, :marca, :validade, :tipo_embalagem, :peso_kg, :descricao, :foto, :fornecedor_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':validade', $validade);
            $stmt->bindParam(':tipo_embalagem', $tipo_embalagem);
            $stmt->bindParam(':peso_kg', $peso_kg);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':foto', $foto_content, PDO::PARAM_LOB);
            $stmt->bindParam(':fornecedor_id', $fornecedor_id);

            $stmt->execute();

            // Redirecionar para o dashboard do fornecedor após o cadastro do produto
            header("Location: fornecedor_dashboard.php");
            exit();
        } else {
            throw new Exception("Por favor, selecione uma imagem.");
        }
    }
} catch (PDOException $e) {
    $message = "Erro de conexão com o banco de dados: " . $e->getMessage();
} catch (Exception $e) {
    $message = "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto - Fornecedor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="fornecedor_dashboard.php">AVOS BRASIL</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="cadastro_produto.php">Cadastar Produto</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="fornecedor_pedidos.php">Meus Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lojista_perfil.php">Meu Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Sair</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Cadastro de Produto</h2>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome do Produto:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="preco">Preço:</label>
            <input type="number" name="preco" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="marca">Marca:</label>
            <input type="text" name="marca" class="form-control">
        </div>
        <div class="form-group">
            <label for="validade">Validade:</label>
            <input type="date" name="validade" class="form-control">
        </div>
        <div class="form-group">
            <label for="tipo_embalagem">Tipo de Embalagem:</label>
            <select name="tipo_embalagem" class="form-control">
                <option value="caixa">Caixa</option>
                <option value="pacote">Pacote</option>
            </select>
        </div>
        <div class="form-group">
            <label for="peso_kg">Peso (em kg):</label>
            <input type="number" name="peso_kg" class="form-control">
        </div>
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="foto">Foto:</label>
            <input type="file" name="foto" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar Produto</button>
    </form>
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
