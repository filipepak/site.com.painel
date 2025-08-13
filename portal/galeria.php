<?php
require 'conexao.php';

// Busca todas as fotos
$fotos = $conn->query("SELECT * FROM galeria_fotos ORDER BY data_upload DESC");
if (!$fotos) {
    die("<div class='alert alert-danger'>Erro ao buscar fotos: " . $conn->error . "</div>");
}
?>

<h2>Galeria de Fotos</h2>
<a href="nova_foto.php" class="btn btn-primary" onclick="carregarConteudo('nova_foto.php'); return false;">Adicionar Foto</a>

<div class="row" style="margin-top:20px;">
    <?php while ($foto = $fotos->fetch_assoc()): ?>
        <div class="col-sm-3" style="margin-bottom:30px;">
            <div class="thumbnail">
                <img src="<?= htmlspecialchars($foto['url']) ?>" alt="Foto" style="width:100%; height:200px; object-fit:cover;">
                <div class="caption">
                    <small><?= date('d/m/Y H:i', strtotime($foto['data_upload'])) ?></small>
                    <p><?= htmlspecialchars($foto['descricao']) ?></p>
                    <div>
                        <button class="btn btn-info btn-xs" onclick="carregarConteudo('editar_foto.php?id=<?= $foto['id'] ?>')">Editar</button>
                        <button class="btn btn-danger btn-xs" onclick="if(confirm('Excluir foto?')) excluirFoto(<?= $foto['id'] ?>)">Excluir</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
function excluirFoto(id) {
    $.post('excluir_foto.php', {id: id}, function(res) {
        if (res.trim() === 'ok') {
            carregarConteudo('galeria.php');
        } else {
            alert('Erro ao excluir foto: ' + res);
        }
    });
}
</script>
