<?php
require 'conexao.php';

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado = isset($_POST['estado']) ? strtoupper(trim($_POST['estado'])) : '';
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $ano = isset($_POST['ano_publicacao']) ? intval($_POST['ano_publicacao']) : null;
    $autores = isset($_POST['autores']) ? trim($_POST['autores']) : '';
    $instituicao = isset($_POST['instituicao']) ? trim($_POST['instituicao']) : '';
    $link = isset($_POST['link']) ? trim($_POST['link']) : '';

    // Upload de arquivo PDF
    $arquivo = null;
    if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid('mapa_') . '.' . $ext;
        $destino = 'uploads/' . $nomeArquivo;
        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
            $arquivo = $nomeArquivo;
        } else {
            $erro = "Erro ao fazer upload do arquivo PDF!";
        }
    }

    // Upload de imagem (capa)
    $imagem = null;
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext_img = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeImagem = uniqid('capa_') . '.' . $ext_img;
        $destinoImg = 'uploads/' . $nomeImagem;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destinoImg)) {
            $imagem = $nomeImagem;
        } else {
            $erro = "Erro ao fazer upload da imagem!";
        }
    }

    if (empty($estado) || empty($tipo) || empty($titulo)) {
        $erro = "Preencha os campos obrigatórios: Estado, Tipo e Título.";
    }
    if (empty($arquivo) && empty($link)) {
        $erro = "Informe um arquivo OU um link!";
    }

    if (empty($erro)) {
        $stmt = $conn->prepare("INSERT INTO mapas (estado, tipo, titulo, descricao, ano_publicacao, autores, instituicao, arquivo, link, imagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssisssss",
            $estado,
            $tipo,
            $titulo,
            $descricao,
            $ano,
            $autores,
            $instituicao,
            $arquivo,
            $link,
            $imagem
        );
        if ($stmt->execute()) {
            $sucesso = "Mapa/Artigo cadastrado com sucesso!";
        } else {
            $erro = "Erro ao salvar no banco: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Mapa/Artigo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .form-group { margin-bottom: 15px; }
        .obg { color: red; font-weight: bold;}
        .container { max-width: 600px; margin: 40px auto; }
    </style>
</head>
<body>
<div class="container">
    <h2>Adicionar Mapa/Artigo</h2>

    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Estado <span class="obg">*</span></label>
            <input type="text" name="estado" maxlength="2" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Tipo <span class="obg">*</span></label>
            <select name="tipo" class="form-control" required>
                <option value="">Selecione</option>
                <option value="artigo">Artigo</option>
                <option value="mestrado">Mestrado</option>
                <option value="doutorado">Doutorado</option>
            </select>
        </div>
        <div class="form-group">
            <label>Título <span class="obg">*</span></label>
            <input type="text" name="titulo" class="form-control" maxlength="255" required>
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control" maxlength="1000"></textarea>
        </div>
        <div class="form-group">
            <label>Ano de Publicação</label>
            <input type="number" name="ano_publicacao" class="form-control" min="1800" max="<?= date('Y') ?>">
        </div>
        <div class="form-group">
            <label>Autores</label>
            <input type="text" name="autores" class="form-control" maxlength="255">
        </div>
        <div class="form-group">
            <label>Instituição</label>
            <input type="text" name="instituicao" class="form-control" maxlength="255">
        </div>
        <div class="form-group">
            <label>Arquivo PDF</label>
            <input type="file" name="arquivo" accept=".pdf" class="form-control">
        </div>
        <div class="form-group">
            <label>OU Link do Artigo/Mestrado/Doutorado</label>
            <input type="url" name="link" class="form-control" placeholder="https://...">
        </div>
        <div class="form-group">
            <label>Imagem (miniatura/capa)</label>
            <input type="file" name="imagem" accept="image/*" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="mapas_listar.php" class="btn btn-default">Voltar</a>
        <p class="obg" style="margin-top:10px;">* Campos obrigatórios</p>
    </form>
</div>
</body>
</html>
