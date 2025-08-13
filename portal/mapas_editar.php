<?php
require 'conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$q = $conn->prepare("SELECT * FROM mapas WHERE id=?");
$q->bind_param("i", $id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
if(!$row) { echo "<div class='alert alert-danger'>Registro não encontrado.</div>"; exit; }

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD']=='POST') {
    $estado = strtoupper(trim($_POST['estado']));
    $tipo = $_POST['tipo'];
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $ano = isset($_POST['ano_publicacao']) ? intval($_POST['ano_publicacao']) : null;
    $autores = isset($_POST['autores']) ? trim($_POST['autores']) : '';
    $instituicao = isset($_POST['instituicao']) ? trim($_POST['instituicao']) : '';
    $link = isset($_POST['link']) ? trim($_POST['link']) : '';

    // Arquivo PDF (mantém o atual se não trocar)
    $arquivo = $row['arquivo'];
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid('mapa_') . '.' . $ext;
        $destino = 'uploads/' . $nomeArquivo;
        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
            $arquivo = $nomeArquivo;
        } else {
            $erro = "Erro ao fazer upload do arquivo PDF!";
        }
    }

    // Imagem (mantém a atual se não trocar)
    $imagem = $row['imagem'];
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $ext_img = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nomeImagem = uniqid('capa_') . '.' . $ext_img;
        $destinoImg = 'uploads/' . $nomeImagem;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destinoImg)) {
            $imagem = $nomeImagem;
        } else {
            $erro = "Erro ao fazer upload da imagem!";
        }
    }

    if (empty($arquivo) && empty($link)) {
        $erro = "Informe um arquivo OU um link!";
    }

    if (empty($erro)) {
        $stmt = $conn->prepare("UPDATE mapas SET estado=?, tipo=?, titulo=?, descricao=?, ano_publicacao=?, autores=?, instituicao=?, arquivo=?, link=?, imagem=? WHERE id=?");
        $stmt->bind_param(
            "ssssisssssi",
            $estado,
            $tipo,
            $titulo,
            $descricao,
            $ano,
            $autores,
            $instituicao,
            $arquivo,
            $link,
            $imagem,
            $id
        );
        if ($stmt->execute()) {
            $sucesso = "Registro atualizado com sucesso!";
            $q = $conn->prepare("SELECT * FROM mapas WHERE id=?");
            $q->bind_param("i", $id);
            $q->execute();
            $row = $q->get_result()->fetch_assoc();
        } else {
            $erro = "Erro ao atualizar no banco: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Mapa/Artigo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .form-group { margin-bottom: 15px; }
        .obg { color: red; font-weight: bold;}
        .container { max-width: 600px; margin: 40px auto; }
    </style>
</head>
<body>
<div class="container">
    <h2>Editar Mapa/Artigo</h2>
    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Estado <span class="obg">*</span></label>
            <input type="text" name="estado" maxlength="2" class="form-control" required value="<?= htmlspecialchars($row['estado']) ?>">
        </div>
        <div class="form-group">
            <label>Tipo <span class="obg">*</span></label>
            <select name="tipo" class="form-control" required>
                <option value="">Selecione</option>
                <option value="artigo" <?= $row['tipo']=='artigo'?'selected':''; ?>>Artigo</option>
                <option value="mestrado" <?= $row['tipo']=='mestrado'?'selected':''; ?>>Mestrado</option>
                <option value="doutorado" <?= $row['tipo']=='doutorado'?'selected':''; ?>>Doutorado</option>
            </select>
        </div>
        <div class="form-group">
            <label>Título <span class="obg">*</span></label>
            <input type="text" name="titulo" class="form-control" maxlength="255" required value="<?= htmlspecialchars($row['titulo']) ?>">
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control" maxlength="1000"><?= htmlspecialchars($row['descricao']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Ano de Publicação</label>
            <input type="number" name="ano_publicacao" class="form-control" min="1800" max="<?= date('Y') ?>" value="<?= htmlspecialchars($row['ano_publicacao']) ?>">
        </div>
        <div class="form-group">
            <label>Autores</label>
            <input type="text" name="autores" class="form-control" maxlength="255" value="<?= htmlspecialchars($row['autores']) ?>">
        </div>
        <div class="form-group">
            <label>Instituição</label>
            <input type="text" name="instituicao" class="form-control" maxlength="255" value="<?= htmlspecialchars($row['instituicao']) ?>">
        </div>
        <div class="form-group">
            <label>Arquivo PDF <?php if ($row['arquivo']): ?>(<a href="uploads/<?= htmlspecialchars($row['arquivo']) ?>" target="_blank">Ver atual</a>)<?php endif; ?></label>
            <input type="file" name="arquivo" accept=".pdf" class="form-control">
        </div>
        <div class="form-group">
            <label>OU Link do Artigo/Mestrado/Doutorado</label>
            <input type="url" name="link" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($row['link']) ?>">
        </div>
        <div class="form-group">
            <label>Imagem (miniatura/capa) <?php if ($row['imagem']): ?>(<a href="uploads/<?= htmlspecialchars($row['imagem']) ?>" target="_blank">Ver atual</a>)<?php endif; ?></label>
            <input type="file" name="imagem" accept="image/*" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="mapas_listar.php" class="btn btn-default">Voltar</a>
        <p class="obg" style="margin-top:10px;">* Campos obrigatórios</p>
    </form>
</div>
</body>
</html>
