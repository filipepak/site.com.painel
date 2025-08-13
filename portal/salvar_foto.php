<?php
require 'conexao.php';

if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    die('Imagem não enviada');
}

$descricao = $_POST['descricao'] ?? '';
$imagem = $_FILES['imagem'];

// Salva imagem na pasta
$nome = uniqid().basename($imagem['name']);
$caminho = "uploads/".$nome;
if (!move_uploaded_file($imagem['tmp_name'], $caminho)) {
    die('Falha ao salvar arquivo');
}

// Salva no banco
$stmt = $conn->prepare("INSERT INTO galeria_fotos (url, descricao, data_upload) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $caminho, $descricao);
if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Erro: ".$stmt->error;
}
