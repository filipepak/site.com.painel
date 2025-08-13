<?php
require 'conexao.php';
$id = intval($_POST['id']);
$conn->query("DELETE FROM galeria_fotos WHERE id=$id");
echo $conn->affected_rows > 0 ? 'ok' : 'Erro ao excluir';
