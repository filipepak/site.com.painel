<?php
require 'conexao.php';
$res = $conn->query("SELECT * FROM artigos ORDER BY data_publicacao DESC, id DESC");
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Título</th>
            <th>Autores</th>
            <th>Tipo</th>
            <th>Data Publicação</th>
            <th>Link/Arquivo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['titulo']) ?></td>
            <td><?= htmlspecialchars($row['autores']) ?></td>
            <td><?= ucfirst($row['tipo']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['data_publicacao'])) ?></td>
            <td>
                <?php if ($row['link']): ?>
                    <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank">Ver Link</a>
                <?php endif; ?>
                <?php if ($row['arquivo']): ?>
                    <a href="<?= htmlspecialchars($row['arquivo']) ?>" target="_blank">Ver Arquivo</a>
                <?php endif; ?>
            </td>
            <td>
                <button class="btn btn-info btn-xs" onclick="abrirEditarArtigo(<?= $row['id'] ?>)">Editar</button>
                <button class="btn btn-danger btn-xs" onclick="excluirArtigo(<?= $row['id'] ?>)">Excluir</button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
