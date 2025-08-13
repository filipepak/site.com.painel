<?php
// Apenas exibe o formulário!
?>
<h2>Adicionar Foto</h2>
<form id="formNovaFoto" enctype="multipart/form-data">
    <div class="form-group">
        <label>Imagem:</label>
        <input type="file" name="imagem" class="form-control" accept="image/*" required>
    </div>
    <div class="form-group">
        <label>Descrição:</label>
        <input type="text" name="descricao" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Salvar Foto</button>
    <button type="button" class="btn btn-default" onclick="carregarConteudo('galeria.php')">Cancelar</button>
</form>

<script>
$("#formNovaFoto").on('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'salvar_foto.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res){
            if(res.trim() === 'ok'){
                carregarConteudo('galeria.php');
            }else{
                alert('Erro ao salvar foto: ' + res);
            }
        }
    });
});
</script>
