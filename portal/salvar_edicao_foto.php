<?php
require 'conexao.php';

$id = intval($_POST['id']);
$descricao = $_POST['descricao'] ?? '';

// Atualiza imagem se foi enviada
if (!empty($_FILES['imagem']['name'])) {
    $imagem = $_FILES['imagem'];
    $nome = uniqid().basename($imagem['name']);
    $caminho = "uploads/".$nome;
    if (!move_uploaded_file($imagem['tmp_name'], $caminho)) {
        die('Falha ao salvar nova imagem');
    }
    $sql = "UPDATE galeria_fotos SET url=?, descricao=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $caminho, $descricao, $id);
} else {
    $sql = "UPDATE galeria_fotos SET descricao=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $descricao, $id);
}
if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Erro: ".$stmt->error;
}
