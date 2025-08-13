<?php
require 'conexao.php';

$titulo = $_POST['titulo'] ?? '';
$data_inicio = $_POST['data_inicio'] ?? '';
$local = $_POST['local'] ?? '';
$status = $_POST['status'] ?? '';

if (!$titulo || !$data_inicio || !$local || !$status) {
    echo "Preencha todos os campos!";
    exit;
}

// Prepare e execute (evita SQL Injection)
$stmt = $conn->prepare("INSERT INTO eventos (titulo, data_inicio, local, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $titulo, $data_inicio, $local, $status);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Erro ao salvar evento!";
}
$stmt->close();
