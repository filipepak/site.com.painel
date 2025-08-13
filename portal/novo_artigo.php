
<form id="formNovoArtigo" enctype="multipart/form-data" method="post">
    <div class="modal-header">
        <h4 class="modal-title">Novo Artigo</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="acao" value="novo">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Autores</label>
            <input type="text" name="autores" class="form-control">
        </div>
        <div class="form-group">
            <label>Resumo</label>
            <textarea name="resumo" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select name="tipo" class="form-control" required>
                <option value="">Selecione</option>
                <option value="artigo">Artigo</option>
                <option value="dissertacao">Dissertação</option>
                <option value="tese">Tese</option>
            </select>
        </div>
        <div class="form-group">
            <label>Data Publicação</label>
            <input type="date" name="data_publicacao" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Link (URL externo, opcional)</label>
            <input type="url" name="link" class="form-control">
        </div>
        <div class="form-group">
            <label>Arquivo PDF/Capa (opcional)</label>
            <input type="file" name="arquivo" class="form-control">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Salvar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
    </div>
</form>
<script>
$('#formNovoArtigo').submit(function(e){
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
