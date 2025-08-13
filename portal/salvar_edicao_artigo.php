<?php
require 'conexao.php';

// Função para upload seguro
function salvarArquivo($campo, $destino = 'uploads/') {
    if (empty($_FILES[$campo]['name'])) return '';
    $nome = uniqid() . '_' . basename($_FILES[$campo]['name']);
    $caminho = $destino . $nome;
    $ext = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
    $permitidas = ['pdf','jpg','jpeg','png'];
    if (!in_array($ext, $permitidas)) return '';
    if (move_uploaded_file($_FILES[$campo]['tmp_name'], $caminho)) {
        return $caminho;
    }
    return '';
}

if ($_POST['acao']=='novo') {
    $titulo = $_POST['titulo'] ?? '';
    $autores = $_POST['autores'] ?? '';
    $resumo = $_POST['resumo'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $data = $_POST['data_publicacao'] ?? '';
    $link = $_POST['link'] ?? '';
    $arquivo = salvarArquivo('arquivo');
    $stmt = $conn->prepare("INSERT INTO artigos (titulo, autores, resumo, tipo, data_publicacao, link, arquivo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $titulo, $autores, $resumo, $tipo, $data, $link, $arquivo);
    $stmt->execute();
    echo "<div class='alert alert-success'>Artigo cadastrado!</div>";
    exit;
}
if ($_POST['acao']=='editar') {
    $id = intval($_POST['id']);
    $titulo = $_POST['titulo'] ?? '';
    $autores = $_POST['autores'] ?? '';
    $resumo = $_POST['resumo'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $data = $_POST['data_publicacao'] ?? '';
    $link = $_POST['link'] ?? '';
    $arquivo = salvarArquivo('arquivo');
    $setArquivo = $arquivo ? ", arquivo='$arquivo'" : '';
    $sql = "UPDATE artigos SET titulo=?, autores=?, resumo=?, tipo=?, data_publicacao=?, link=? $setArquivo WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($arquivo) {
        $stmt->bind_param("ssssssi", $titulo, $autores, $resumo, $tipo, $data, $link, $id);
    } else {
        $stmt->bind_param("ssssssi", $titulo, $autores, $resumo, $tipo, $data, $link, $id);
    }
    $stmt->execute();
    echo "<div class='alert alert-success'>Artigo atualizado!</div>";
    exit;
}
?>
