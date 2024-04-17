<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $query = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Atualizar o último acesso do usuário
        $horario_acesso = date('Y-m-d H:i:s');
        $sql = "UPDATE `usuarios` SET `ultimo_acesso` = '$horario_acesso' WHERE `id` = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $user['id']);
        $stmt->execute();

        // Definir as variáveis de sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nivel_acesso'] = $user['nivel_acesso'];
        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Credenciais inválidas. Tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/vetores-gratis/vetor-de-fundo-de-padrao-geometrico-branco-e-cinza_53876-136510.jpg?size=626&ext=jpg&ga=GA1.1.735520172.1712102400&semt=sph');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            opacity: 0.6; /* Ajuste a opacidade conforme necessário */
            filter: alpha(opacity=80); /* Para navegadores antigos */
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8); /* Cor de fundo com opacidade */
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" class="form-control" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <?php if(isset($erro)) echo "<p class='text-danger text-center mt-3'>$erro</p>"; ?>
        <p class="mt-3 text-center">Ainda não possui uma conta? <a href="cadastro_usuario.php">Cadastre-se aqui</a>.</p>
    </div>
</body>
</html>
