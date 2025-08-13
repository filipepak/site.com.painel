<?php
require 'conexao.php';
$id = intval($_POST['id'] ?? 0);
if ($id) {
    $res = $conn->query("SELECT arquivo FROM artigos WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if ($row['arquivo'] && file_exists($row['arquivo'])) unlink($row['arquivo']);
    }
    $conn->query("DELETE FROM artigos WHERE id=$id");
    echo "<div class='alert alert-success'>Artigo excluído!</div>";
}
?>
