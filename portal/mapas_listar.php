<?php
require 'conexao.php';

// Excluir registro (via GET, caso queira permitir dentro do modal)
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $stmt = $conn->prepare("DELETE FROM mapas WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // Redireciona para evitar reenvio e recarregar a listagem
    header("Location: mapas_listar.php?msg=excluido");
    exit;
}

// Buscar todos os mapas/artigos
$result = $conn->query("SELECT * FROM mapas ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listagem de Mapas / Artigos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .table th, .table td { vertical-align: middle; }
        .table-responsive { margin-top: 10px;}
        .btn-xs { margin-right: 3px;}
        .modal-body { padding: 18px 12px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <h3 style="margin-top:5px;">Mapas / Artigos Cadastrados</h3>
    <?php if (!empty($_GET['msg']) && $_GET['msg']=='excluido'): ?>
        <div class="alert alert-success">Registro excluído com sucesso!</div>
    <?php endif; ?>
    <!-- Botão Adicionar novo abre a tela normal fora do modal para suportar upload -->
    <a href="mapas_adicionar.php" class="btn btn-success btn-sm" style="margin-bottom:12px;" target="_blank">
        <span class="glyphicon glyphicon-plus"></span> Adicionar novo
    </a>
    <div class="table-responsive">
    <table class="table table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>Estado</th>
            <th>Tipo</th>
            <th>Título</th>
            <th>Ano</th>
            <th>Arquivo</th>
            <th>Link</th>
            <th style="width:110px;">Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['estado']) ?></td>
            <td><?= htmlspecialchars(ucfirst($row['tipo'])) ?></td>
            <td><?= htmlspecialchars($row['titulo']) ?></td>
            <td><?= htmlspecialchars($row['ano_publicacao']) ?></td>
            <td>
                <?php if ($row['arquivo']): ?>
                    <a href="<?= htmlspecialchars($row['arquivo']) ?>" target="_blank" class="btn btn-default btn-xs">Baixar</a>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($row['link']): ?>
                    <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="btn btn-info btn-xs">Abrir</a>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
            <td>
                <!-- Edição e exclusão fora do modal -->
                <a href="mapas_editar.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-xs" target="_blank">Editar</a>
                <a href="mapas_listar.php?excluir=<?= $row['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Excluir este registro?');">Excluir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-warning">Nenhum mapa/artigo cadastrado ainda.</div>
    <?php endif; ?>
</div>
</body>
</html>
