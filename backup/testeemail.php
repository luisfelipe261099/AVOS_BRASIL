<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Envio de E-mail</title>
</head>
<body>

    <h2>Enviar E-mail</h2>

    <form action="" method="post">
        <label for="subject">Assunto:</label><br>
        <input type="text" id="subject" name="subject" required><br><br>
        <input type="submit" name="send" value="Enviar">
    </form>

    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';

    if (isset($_POST["send"])) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();  
            $mail->Host       = 'email-ssl.com.br'; // Servidor SMTP da Locaweb
            $mail->SMTPAuth   = true;
            $mail->Username   = 'suporte@escritorioimperial.com.br'; // Seu endereço de e-mail da Locaweb
            $mail->Password   = 'Sup@202323'; // Sua senha da Locaweb
            $mail->SMTPSecure = 'ssl'; // Tipo de criptografia SSL
            $mail->Port       = 465; // Porta SSL

            // Remetente
            $mail->setFrom('suporte@escritorioimperial.com.br', 'Remetente');

            // Destinatário
            $mail->addAddress('bambamlfmmm@gmail.com', 'Destinatário');

            // Conteúdo
            $mail->isHTML(true); 
            $mail->Subject = $_POST["subject"];
            $mail->Body    = 'Este é um e-mail de teste enviado via PHPMailer usando o e-mail suporte@escritorioimperial.com.br como remetente.';

            // Enviar e-mail
            $mail->send();
            echo "<script>alert('E-mail enviado com sucesso!');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Erro ao enviar e-mail: {$mail->ErrorInfo}');</script>";
        }
    }
    ?>

</body>
</html>
