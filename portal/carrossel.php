<?php
require 'conexao.php';
?>
<meta charset="UTF-8">
<title>Carrossel / Banners</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<style>
.carrossel-img { width:120px; height:60px; object-fit:cover; border-radius:8px;}
</style>

<div class="container">
  <h2>Banners do Carrossel</h2>

  <!-- Formulário de cadastro AJAX -->
  <form id="formCarrossel" enctype="multipart/form-data" class="form-inline" style="margin-bottom:20px;">
    <input type="file" name="imagem" class="form-control" required>
    <input type="text" name="titulo" class="form-control" placeholder="Título" required>
    <input type="text" name="descricao" class="form-control" placeholder="Descrição" required>
    <button type="submit" class="btn btn-success">Adicionar Banner</button>
    <input type="hidden" name="acao" value="adicionar">
  </form>

  <div id="carrossel-msg"></div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Imagem</th>
        <th>Título</th>
        <th>Descrição</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody id="carrossel-lista">
      <!-- AJAX renderiza aqui -->
    </tbody>
  </table>
</div>

<!-- Modal Edição AJAX -->
<div class="modal fade" id="modalEditarCarrossel" tabindex="-1" role="dialog"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
function carregarCarrossel() {
  $.get('carrossel_acao.php?acao=listar', function(html){
    $("#carrossel-lista").html(html);
  });
}
carregarCarrossel();

$("#formCarrossel").submit(function(e){
  e.preventDefault();
  var formData = new FormData(this);
  $.ajax({
    url: 'carrossel_acao.php',
    type: 'POST',
    data: formData,
    contentType: false, processData: false,
    success: function(msg) {
      $("#carrossel-msg").html(msg);
      carregarCarrossel();
      $("#formCarrossel")[0].reset();
    }
  });
});

// Excluir
$(document).on("click", ".btn-excluir", function(){
  if (!confirm("Excluir este banner?")) return;
  var id = $(this).data("id");
  $.post('carrossel_acao.php', {acao:'excluir', id:id}, function(msg){
    $("#carrossel-msg").html(msg);
    carregarCarrossel();
  });
});

// Editar - Modal
$(document).on("click", ".btn-editar", function(){
  var id = $(this).data("id");
  $.get('carrossel_acao.php?acao=form_editar&id='+id, function(modalHtml){
    $("#modalEditarCarrossel").html(modalHtml).modal('show');
  });
});

// Salvar edição
$(document).on("submit", "#formEditarCarrossel", function(e){
  e.preventDefault();
  var formData = new FormData(this);
  formData.append('acao', 'editar');
  $.ajax({
    url: 'carrossel_acao.php',
    type: 'POST',
    data: formData,
    contentType: false, processData: false,
    success: function(msg) {
      $("#carrossel-msg").html(msg);
      $("#modalEditarCarrossel").modal('hide');
      carregarCarrossel();
    }
  });
});
</script>
