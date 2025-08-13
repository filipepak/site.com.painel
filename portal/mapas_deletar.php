<?php
require 'conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo "<div class='alert alert-danger'>ID inválido!</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM mapas WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: mapas_listar.php?msg=excluido");
    exit;
}

$q = $conn->prepare("SELECT * FROM mapas WHERE id=?");
$q->bind_param("i", $id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
if (!$row) { echo "<div class='alert alert-danger'>Registro não encontrado.</div>"; exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Excluir Registro</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>.container { max-width: 500px; margin: 50px auto; }</style>
</head>
<body>
<div class="container">
    <h3>Tem certeza que deseja excluir?</h3>
    <p><b><?= htmlspecialchars($row['titulo']) ?></b> (<?= htmlspecialchars($row['estado']) ?>, <?= htmlspecialchars($row['tipo']) ?>)</p>
    <form method="post">
        <button type="submit" class="btn btn-danger">Sim, Excluir</button>
        <a href="mapas_listar.php" class="btn btn-default">Cancelar</a>
    </form>
</div>
</body>
</html>
