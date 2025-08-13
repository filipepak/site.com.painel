<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Artigos Acadêmicos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .table td { vertical-align: middle; }
        .artigo-capa { width: 60px; height: 60px; object-fit: cover; border-radius: 4px;}
    </style>
</head>
<body>
<div class="container">
    <h2>Artigos / Trabalhos Acadêmicos</h2>
    <button class="btn btn-success" onclick="abrirNovoArtigo()">+ Novo Artigo</button>
    <div id="msg-artigo"></div>
    <div id="lista-artigos"></div>
</div>

<!-- Modal de cadastro/edição -->
<div id="modalArtigo" class="modal fade" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"></div></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
function carregarArtigos() {
    $("#lista-artigos").html("Carregando...");
    $.get('listar_artigos.php', function(html){
        $("#lista-artigos").html(html);
    });
}
function abrirNovoArtigo() {
    $.get('novo_artigo.php', function(form){
        $("#modalArtigo .modal-content").html(form);
        $("#modalArtigo").modal('show');
    });
}
function abrirEditarArtigo(id) {
    $.get('editar_artigo_ajax.php?id='+id, function(form){
        $("#modalArtigo .modal-content").html(form);
        $("#modalArtigo").modal('show');
    });
}
function excluirArtigo(id) {
    if(confirm('Tem certeza que deseja excluir?')) {
        $.post('excluir_artigos.php', {id:id}, function(resp){
            carregarArtigos();
            $("#msg-artigo").html(resp);
        });
    }
}
$(function(){ carregarArtigos(); });
</script>
</body>
</html>
