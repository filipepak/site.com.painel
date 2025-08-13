<?php
require 'conexao.php';

$id = intval($_GET['id'] ?? 0);
$foto = $conn->query("SELECT * FROM galeria_fotos WHERE id=$id")->fetch_assoc();
if (!$foto) { echo "<div class='alert alert-danger'>Foto não encontrada.</div>"; exit; }
?>
<h2>Editar Foto</h2>
<form id="formEditaFoto" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $foto['id'] ?>">
    <div class="form-group">
        <label>Imagem atual:</label><br>
        <img src="<?= htmlspecialchars($foto['url']) ?>" style="width:200px;"><br>
        <label>Nova Imagem (opcional):</label>
        <input type="file" name="imagem" class="form-control" accept="image/*">
    </div>
    <div class="form-group">
        <label>Descrição:</label>
        <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($foto['descricao']) ?>">
    </div>
    <button type="submit" class="btn btn-success">Salvar Alterações</button>
    <button type="button" class="btn btn-default" onclick="carregarConteudo('galeria.php')">Cancelar</button>
</form>
<script>
$("#formEditaFoto").on('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'salvar_edicao_foto.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
            if(res.trim() === 'ok'){
                carregarConteudo('galeria.php');
            }else{
                alert('Erro ao salvar: ' + res);
            }
        }
    });
});
</script>
