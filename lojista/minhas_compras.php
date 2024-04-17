<?php
// Inclua a classe do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

session_start();
require_once 'db.php';

// Verifica se o usuário está logado como lojista
if (!isset($_SESSION['user_id']) || $_SESSION['nivel_acesso'] != 'lojista') {
    header("Location: login.php");
    exit();
}

// Faz logout se o parâmetro de logout for passado
if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Define o número de pedidos por página
$pedidosPorPagina = 5;
$paginaAtual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $pedidosPorPagina;

// Verificar se o lojista confirmou a compra
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['confirmar_compra'])) {
    $compra_id = $_GET['confirmar_compra'];
    $query = "UPDATE compras SET status_pedido = 'confirmado' WHERE id = :compra_id AND status_pedido = 'pendente'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':compra_id', $compra_id);
    $stmt->execute();

    // Enviar email
    $mail = new PHPMailer(true);
    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'email-ssl.com.br'; // Servidor SMTP da Locaweb
        $mail->SMTPAuth = true;
        $mail->Username = 'suporte@escritorioimperial.com.br'; // Seu endereço de e-mail da Locaweb
        $mail->Password = 'Sup@202323'; // Sua senha da Locaweb
        $mail->SMTPSecure = 'ssl'; // Tipo de criptografia SSL
        $mail->Port = 465; // Porta SSL

        // Destinatário
        $mail->setFrom('suporte@escritorioimperial.com.br', 'Sua Loja');
        
        // Consulta para obter o email do fornecedor
        $consulta_fornecedor = "SELECT usuarios.email FROM compras 
                                JOIN usuarios ON compras.fornecedor_id = usuarios.id
                                WHERE compras.id = :compra_id";
        $stmt_fornecedor = $pdo->prepare($consulta_fornecedor);
        $stmt_fornecedor->bindParam(':compra_id', $compra_id);
        $stmt_fornecedor->execute();
        $email_fornecedor = $stmt_fornecedor->fetch(PDO::FETCH_ASSOC)['email'];
        
        $mail->addAddress($email_fornecedor); // Email do fornecedor obtido na consulta

        // Conteúdo do email
        $lojista_id = $_SESSION['user_id'];
        $usuario_query = "SELECT cnpj, nome, telefone, endereco FROM usuarios WHERE id = :lojista_id";
        $stmt_usuario = $pdo->prepare($usuario_query);
        $stmt_usuario->bindParam(':lojista_id', $lojista_id);
        $stmt_usuario->execute();
        $dados_lojista = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

        $compra_query = "SELECT produtos.nome AS produto_nome, compras.quantidade FROM compras 
                            JOIN produtos ON compras.produto_id = produtos.id 
                            WHERE compras.id = :compra_id";
        $stmt_compra = $pdo->prepare($compra_query);
        $stmt_compra->bindParam(':compra_id', $compra_id);
        $stmt_compra->execute();
        $dados_compra = $stmt_compra->fetch(PDO::FETCH_ASSOC);

        $mail->isHTML(true);
        $mail->Subject = 'AVOS DO BRASIL Novo Pedido confirmado';
        $mail->Body = 'INFORMAÇÃO DO PEDIDO:<br>' . 
                      'Produto: ' . $dados_compra['produto_nome'] . '<br>' . 
                      'Quantidade: ' . $dados_compra['quantidade'] . '<br>' . 
                      'CNPJ: ' . $dados_lojista['cnpj'] . '<br>' . 
                      'Nome: ' . $dados_lojista['nome'] . '<br>' . 
                      'Telefone: ' . $dados_lojista['telefone'] . '<br>' . 
                      'Endereço: ' . $dados_lojista['endereco'];

        $mail->send();
    } catch (Exception $e) {
        echo "<script>alert('Ocorreu um erro ao enviar o email: {$mail->ErrorInfo}');</script>";
    }

    // Redirecionar de volta para a página
    header("Location: minhas_compras.php");
    exit();
}

// Excluir pedido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_pedido'])) {
    $compra_id = $_POST['compra_id'];
    $query = "DELETE FROM compras WHERE id = :compra_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':compra_id', $compra_id);
    $stmt->execute();
}

// Consulta para obter o número total de pedidos
$lojista_id = $_SESSION['user_id'];
$queryTotal = "SELECT COUNT(*) AS total FROM compras WHERE lojista_id = :lojista_id";
$stmtTotal = $pdo->prepare($queryTotal);
$stmtTotal->bindParam(':lojista_id', $lojista_id);
$stmtTotal->execute();
$totalPedidos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

// Consulta para obter os pedidos da página atual
$queryPedidos = "SELECT compras.*, produtos.nome AS produto_nome, compras.valor_pedido AS produto_preco, usuarios.nome AS fornecedor_nome
          FROM compras
          JOIN produtos ON compras.produto_id = produtos.id
          JOIN usuarios ON compras.fornecedor_id = usuarios.id
          WHERE compras.lojista_id = :lojista_id
          ORDER BY compras.id DESC
          LIMIT :pedidosPorPagina OFFSET :offset";
$stmtPedidos = $pdo->prepare($queryPedidos);
$stmtPedidos->bindParam(':lojista_id', $lojista_id);
$stmtPedidos->bindParam(':pedidosPorPagina', $pedidosPorPagina, PDO::PARAM_INT);
$stmtPedidos->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmtPedidos->execute();
$compras = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);
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
            <img src="https://www.friboi.com.br/_next/image/?url=https%3A%2F%2Ffuture-brand-frib.s3.amazonaws.com%2F1953_friboi_simplismente_excepcional_hero_desktop_0bbcd07507.png&w=1440&q=85" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item">
            <img src="https://www.friboi.com.br/_next/image/?url=https%3A%2F%2Ffuture-brand-frib.s3.amazonaws.com%2F1953_friboi_simplismente_excepcional_hero_desktop_0bbcd07507.png&w=1440&q=85" class="d-block w-100" alt="Slide 2">
        </div>
        <!-- Adicione mais slides conforme necessário -->
    </div>
</div>

<div class="container mt-5">
    <div class="jumbotron text-center">
        <h1 class="display-4">Minhas Compras</h1>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Fornecedor</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $compra): ?>
                        <tr>
                            <td><?php echo $compra['produto_nome'] ?? 'N/A'; ?></td>
                            <td><?php echo $compra['fornecedor_nome'] ?? 'N/A'; ?></td>
                            <td><?php echo $compra['quantidade'] ?? 'N/A'; ?></td>
                            <td><?php echo isset($compra['produto_preco']) ? 'R$ ' . number_format($compra['produto_preco'], 2, ',', '.') : 'N/A'; ?></td>
                            <td><?php echo isset($compra['produto_preco']) && isset($compra['quantidade']) ? 'R$ ' . number_format($compra['produto_preco'] * $compra['quantidade'], 2, ',', '.') : 'N/A'; ?></td>
                            <td><?php echo $compra['status_pedido'] ?? 'N/A'; ?></td>
                            <td>
                                <?php if ($compra['status_pedido'] == 'pendente'): ?>
                                    <a href="?confirmar_compra=<?php echo $compra['id']; ?>" class="btn btn-sm btn-success" onclick="return confirmarPedido()">Confirmar</a>
                                    <!-- Formulário para excluir pedido -->
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="d-inline">
                                        <input type="hidden" name="compra_id" value="<?php echo $compra['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" name="excluir_pedido">Excluir</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if($totalPedidos > $pedidosPorPagina): ?>
                <div class="text-center mt-3">
                    <nav aria-label="Navegação de páginas">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($paginaAtual <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=1" aria-label="Primeira página">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Primeira página</span>
                                </a>
                            </li>
                            <li class="page-item <?php echo ($paginaAtual <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo ($paginaAtual > 1) ? ($paginaAtual - 1) : 1; ?>" aria-label="Página anterior">
                                    <span aria-hidden="true">&lsaquo;</span>
                                    <span class="sr-only">Página anterior</span>
                                </a>
                            </li>
                            <li class="page-item <?php echo ($paginaAtual >= ceil($totalPedidos / $pedidosPorPagina)) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo ($paginaAtual < ceil($totalPedidos / $pedidosPorPagina)) ? ($paginaAtual + 1) : ceil($totalPedidos / $pedidosPorPagina); ?>" aria-label="Próxima página">
                                    <span aria-hidden="true">&rsaquo;</span>
                                    <span class="sr-only">Próxima página</span>
                                </a>
                            </li>
                            <li class="page-item <?php echo ($paginaAtual >= ceil($totalPedidos / $pedidosPorPagina)) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo ceil($totalPedidos / $pedidosPorPagina); ?>" aria-label="Última página">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Última página</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
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
<script>
    function confirmarPedido() {
        return confirm('Pedido confirmado e encaminhado para entrega');
    }
</script>
</body>
</html>
