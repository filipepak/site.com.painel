<?php
// conexao.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'portal_futebol'; // troque pelo nome do seu banco

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Erro ao conectar ao banco de dados: ' . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
