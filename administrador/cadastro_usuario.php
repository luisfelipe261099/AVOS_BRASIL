<?php 
session_start();
require_once 'db.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$mensagem = $mensagem_email = '';

$bairros_permitidos = [
    'Abranches', 'Água Verde', 'Ahú', 'Alto Boqueirão', 'Alto da Glória', 'Alto da XV', 'Atuba', 'Augusta',
    'Bacacheri', 'Bairro Alto', 'Barreirinha', 'Batel', 'Boa Vista', 'Bom Retiro', 'Boqueirão', 'Butiatuvinha',
    'Cabral', 'Cachoeira', 'Cajuru', 'Campina do Siqueira', 'Campo Comprido', 'Campo de Santana', 'Capão da Imbuia',
    'Capão Raso', 'Cascatinha', 'Centro', 'Centro Histórico', 'Caximba', 'Centro Cívico', 'Champagnat',
    'Cidade Industrial', 'Cristo Rei', 'Fanny', 'Fazendinha', 'Ganchinho', 'Guabirotuba', 'Guaíra', 'Hauer',
    'Hugo Lange', 'Jardim Botânico', 'Jardim Social', 'Jardim das Américas', 'Juvevê', 'Lamenha Pequena', 'Lindóia',
    'Mercês', 'Mossunguê (Ecoville)', 'Novo Mundo', 'Orleans', 'Parolin', 'Pilarzinho', 'Pinheirinho', 'Portão',
    'Prado Velho', 'Rebouças', 'Riviera', 'Santa Cândida', 'Santa Felicidade', 'Santa Quitéria', 'Santo Inácio',
    'São Braz', 'São Francisco', 'São João', 'São Lourenço', 'São Miguel', 'Seminário', 'Sítio Cercado',
    'Taboão', 'Tarumã', 'Tatuquara', 'Tingui', 'Uberaba', 'Umbará', 'Vila Izabel', 'Vista Alegre', 'Xaxim'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['nome'], $_POST['email'], $_POST['senha'], $_POST['nivel_acesso'], $_POST['bairro'], $_POST['cnpj'], $_POST['endereco'], $_POST['telefone'], $_POST['ramo_atuacao'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $nivel_acesso = $_POST['nivel_acesso'];
        $bairro = $_POST['bairro'];
        $cnpj = $_POST['cnpj'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];
        $ramo_atuacao = $_POST['ramo_atuacao'];

        if (!in_array($bairro, $bairros_permitidos)) {
            $mensagem = "Bairro selecionado não é válido.";
        } else {
            try {
                $query = "INSERT INTO usuarios (nome, email, senha, nivel_acesso, bairro, cnpj, endereco, telefone, ramo_atuacao) 
                          VALUES (:nome, :email, :senha, :nivel_acesso, :bairro, :cnpj, :endereco, :telefone, :ramo_atuacao)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', $senha);
                $stmt->bindParam(':nivel_acesso', $nivel_acesso);
                $stmt->bindParam(':bairro', $bairro);
                $stmt->bindParam(':cnpj', $cnpj);
                $stmt->bindParam(':endereco', $endereco);
                $stmt->bindParam(':telefone', $telefone);
                $stmt->bindParam(':ramo_atuacao', $ramo_atuacao);
                $stmt->execute();
                $mensagem = "Usuário cadastrado com sucesso!";
                
                // Enviar email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'email-ssl.com.br';
                $mail->SMTPAuth = true;
                $mail->Username = 'suporte@escritorioimperial.com.br';
                $mail->Password = 'Sup@202323';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('suporte@escritorioimperial.com.br', 'AVOS BRASIL');
                $mail->addAddress('bambamlfmmm@gmail.com');

                $mail->isHTML(true);
                $mail->Subject = 'Novo usuário cadastrado';
                $mail->Body = 'Novo usuário cadastrado: Nome: ' . $nome . ', Email: ' . $email . ', Nível de Acesso: ' . $nivel_acesso . ', Bairro: ' . $bairro;

                $mail->send();
                $mensagem_email = "Bem Vindo a AVOS BRASIL!";
                
                // Redirecionar para a página desejada
                header('Location: https://mpago.la/25oXn5n');
                exit;
                
            } catch (PDOException $e) {
                $mensagem = "Erro ao cadastrar usuário: " . $e->getMessage();
            } catch (Exception $e) {
                $mensagem_email = "Erro ao enviar e-mail: " . $e->getMessage();
            }
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    }
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
    <a class="navbar-brand" href="#">AVOS BRASIL</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
            </li>
        </ul>       
    </div>
</nav>

<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="1.png" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item">
            <img src="1.png" class="d-block w-100" alt="Slide 2">
        </div>
    </div>
</div>

<div class="container">
    <h2 class="mt-5">Cadastro de Usuário</h2>
    <?php if(isset($mensagem)): ?>
        <div class="alert alert-<?php echo strpos($mensagem, 'sucesso') !== false ? 'success' : 'danger'; ?>" role="alert">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($mensagem_email)): ?>
        <div class="alert alert-<?php echo strpos($mensagem_email, 'sucesso') !== false ? 'success' : 'danger'; ?>" role="alert">
            <?php echo $mensagem_email; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mt-4">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="nivel_acesso">Nível de Acesso:</label>
            <select name="nivel_acesso" class="form-control" required>
                <option value="administrador">Administrador</option>
                <option value="fornecedor">Fornecedor</option>
                <option value="lojista">Lojista</option>
            </select>
        </div>
        <div class="form-group">
            <label for="bairro">Bairro:</label>
            <select name="bairro" class="form-control" required>
                <?php foreach ($bairros_permitidos as $bairro_option): ?>
                    <option value="<?php echo $bairro_option; ?>"><?php echo $bairro_option; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="cnpj">CNPJ:</label>
            <input type="text" name="cnpj" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input type="tel" name="telefone" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="ramo_atuacao">Ramo de Atuação:</label>
            <input type="text" name="ramo_atuacao" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
