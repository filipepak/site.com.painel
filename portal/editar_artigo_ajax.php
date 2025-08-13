<?php
require 'conexao.php';
$id = intval($_GET['id'] ?? 0);
$row = $conn->query("SELECT * FROM artigos WHERE id=$id")->fetch_assoc();
if (!$row) { echo "Artigo não encontrado."; exit; }
?>
<form id="formEditarArtigo" enctype="multipart/form-data" method="post">
    <div class="modal-header">
        <h4 class="modal-title">Editar Artigo</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="acao" value="editar">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($row['titulo']) ?>" required>
        </div>
        <div class="form-group">
            <label>Autores</label>
            <input type="text" name="autores" class="form-control" value="<?= htmlspecialchars($row['autores']) ?>">
        </div>
        <div class="form-group">
            <label>Resumo</label>
            <textarea name="resumo" class="form-control"><?= htmlspecialchars($row['resumo']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select name="tipo" class="form-control" required>
                <option value="artigo" <?= $row['tipo']=='artigo'?'selected':'' ?>>Artigo</option>
                <option value="dissertacao" <?= $row['tipo']=='dissertacao'?'selected':'' ?>>Dissertação</option>
                <option value="tese" <?= $row['tipo']=='tese'?'selected':'' ?>>Tese</option>
            </select>
        </div>
        <div class="form-group">
            <label>Data Publicação</label>
            <input type="date" name="data_publicacao" class="form-control" value="<?= htmlspecialchars($row['data_publicacao']) ?>" required>
        </div>
        <div class="form-group">
            <label>Link</label>
            <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($row['link']) ?>">
        </div>
        <div class="form-group">
            <label>Arquivo Atual: 
            <?php if($row['arquivo']): ?>
                <a href="<?= htmlspecialchars($row['arquivo']) ?>" target="_blank">Ver Arquivo</a>
            <?php else: ?>Nenhum<?php endif; ?>
            </label>
            <input type="file" name="arquivo" class="form-control">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Salvar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
    </div>
</form>
<script>
$('#formEditarArtigo').submit(function(e){
    e.preventDefault();
    var form = this;
    var data = new FormData(form);
    $.ajax({
        url:'salvar_edicao_artigo.php',
        type:'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function(resp){
            $('#modalArtigo').modal('hide');
            carregarArtigos();
            $("#msg-artigo").html(resp);
        }
    });
});
</script>
