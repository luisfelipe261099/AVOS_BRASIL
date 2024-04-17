<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
</head>
<body>
    <h1>Editar Usuário</h1>
    <?php
    // Conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "projeto";
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Consulta o usuário com ID 2 no banco de dados
    $id = 2;
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" value="<?php echo $row["nome"]; ?>"><br>
        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" value="<?php echo $row["email"]; ?>"><br>
        <label for="cnpj">CNPJ:</label><br>
        <input type="text" id="cnpj" name="cnpj" value="<?php echo $row["cnpj"]; ?>"><br>
        <label for="ramo_atuacao">Ramo de Atuação:</label><br>
        <input type="text" id="ramo_atuacao" name="ramo_atuacao" value="<?php echo $row["ramo_atuacao"]; ?>"><br>
        <label for="endereco">Endereço:</label><br>
        <input type="text" id="endereco" name="endereco" value="<?php echo $row["endereco"]; ?>"><br>
        <label for="telefone">Telefone:</label><br>
        <input type="text" id="telefone" name="telefone" value="<?php echo $row["telefone"]; ?>"><br><br>
        <input type="submit" value="Salvar">
    </form>
    <br>
    <form method="post" action="excluir_usuario.php">
        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
        <input type="submit" value="Excluir">
    </form>
    <?php
    } else {
        echo "Usuário não encontrado.";
    }
    $conn->close();
    ?>
</body>
</html>
