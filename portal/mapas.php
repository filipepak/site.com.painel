<?php
include_once('conexao.php');

// Filtros
$estado = $_GET['estado'] ?? '';
$tipo = $_GET['tipo'] ?? '';

// Para o select de siglas já cadastradas:
$siglas = [];
$q = $conn->query("SELECT DISTINCT estado FROM mapas ORDER BY estado");
while($r = $q->fetch_assoc()) $siglas[] = $r['estado'];

// Consulta dinâmica
$sql = "SELECT * FROM mapas WHERE 1";
$params = [];
$types = "";
if($estado) {
    $sql .= " AND estado=?";
    $params[] = $estado;
    $types .= "s";
}
if($tipo && in_array($tipo, ['artigo','mestrado','doutorado'])) {
    $sql .= " AND tipo=?";
    $params[] = $tipo;
    $types .= "s";
}
$sql .= " ORDER BY ano_publicacao DESC, id DESC";
$stmt = $conn->prepare($sql);
if($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
  <h2 style="margin:0;">Artigos/Mestrados/Doutorados por Estado</h2>
  <button onclick="carregarConteudo('mapas_adicionar.php')" class="btn btn-success">+ Novo Artigo</button>
</div>
<form method="get" id="form-filtro-mapas" style="margin-bottom:18px;display:flex;gap:12px;">
  <select name="estado" class="form-control" style="max-width:100px;">
    <option value="">Todos estados</option>
    <?php foreach($siglas as $s): ?>
      <option value="<?= $s ?>" <?= $estado==$s?'selected':'' ?>><?= $s ?></option>
    <?php endforeach; ?>
  </select>
  <select name="tipo" class="form-control" style="max-width:120px;">
    <option value="">Todos os tipos</option>
    <option value="artigo" <?= $tipo=='artigo'?'selected':'' ?>>Artigo</option>
    <option value="mestrado" <?= $tipo=='mestrado'?'selected':'' ?>>Mestrado</option>
    <option value="doutorado" <?= $tipo=='doutorado'?'selected':'' ?>>Doutorado</option>
  </select>
  <button type="submit" class="btn btn-default">Filtrar</button>
</form>
<table class="table table-striped table-bordered" style="background:#fff;">
  <thead>
    <tr>
      <th>ID</th>
      <th>Sigla</th>
      <th>Tipo</th>
      <th>Título</th>
      <th>Ano</th>
      <th>Arquivo</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['estado'] ?></td>
        <td><?= ucfirst($row['tipo']) ?></td>
        <td><?= htmlspecialchars($row['titulo']) ?></td>
        <td><?= $row['ano_publicacao'] ?></td>
        <td>
          <?php if($row['arquivo']): ?>
            <a href="uploads/<?= urlencode($row['arquivo']) ?>" target="_blank">Ver Arquivo</a>
          <?php else: ?>
            <span style="color:#bbb;">---</span>
          <?php endif; ?>
        </td>
        <td>
          <button onclick="carregarConteudo('mapas_editar.php?id=<?= $row['id'] ?>')" class="btn btn-xs btn-info">Editar</button>
          <button onclick="carregarConteudo('mapas_deletar.php?id=<?= $row['id'] ?>')" class="btn btn-xs btn-danger">Excluir</button>
        </td>
      </tr>
    <?php endwhile; ?>
    <?php if ($result->num_rows == 0): ?>
      <tr><td colspan="7" style="text-align:center;color:#a00;">Nenhum registro encontrado.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
<script>
$("#form-filtro-mapas").submit(function(e){
  e.preventDefault();
  carregarConteudo('mapas.php?' + $(this).serialize());
});
</script>
