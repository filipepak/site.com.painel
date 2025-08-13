<?php
// novo_evento.php
// Só exibe o formulário!

// Estados disponíveis para select
$estados = [
    'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA',
    'PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'
];
?>
<div class="panel panel-default">
  <div class="panel-heading"><b>Novo Evento</b></div>
  <div class="panel-body">
    <form id="formNovoEvento">
      <div class="form-group">
        <label for="titulo">Título:</label>
        <input type="text" class="form-control" name="titulo" id="titulo" required>
      </div>
      <div class="form-group">
        <label for="descricao">Descrição:</label>
        <textarea class="form-control" name="descricao" id="descricao" rows="3" required></textarea>
      </div>
      <div class="form-group">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado" class="form-control" required>
          <option value="">Selecione o estado</option>
          <?php foreach ($estados as $uf): ?>
            <option value="<?= $uf ?>"><?= $uf ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="data_inicio">Data Início:</label>
        <input type="date" class="form-control" name="data_inicio" id="data_inicio" required>
      </div>
      <div class="form-group">
        <label for="data_fim">Data Fim:</label>
        <input type="date" class="form-control" name="data_fim" id="data_fim">
      </div>
      <button type="submit" class="btn btn-success">Salvar</button>
      <button type="button" class="btn btn-default" onclick="carregarConteudo('eventos.php')">Cancelar</button>
    </form>
  </div>
</div>
<script>
$('#formNovoEvento').on('submit', function(e){
    e.preventDefault();
    $.post('salvar_evento.php', $(this).serialize(), function(resp){
        if (resp === 'ok') {
            carregarConteudo('eventos.php');
        } else {
            alert(resp);
        }
    });
});
</script>
