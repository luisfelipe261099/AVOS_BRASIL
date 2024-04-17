<?php
$host = 'localhost';
$dbname = 'id22022220_sistem_avos';
$username = 'id22022220_sistemaavos';  
$password = 'T3cn0l0g1a@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
