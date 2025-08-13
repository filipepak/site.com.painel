<?php 
require 'conexao.php';

// Filtros
$status = $_GET['status'] ?? '';
$data = $_GET['data'] ?? '';

// Monta a query
$sql = "SELECT * FROM eventos WHERE 1";
if ($status) $sql .= " AND status = '".$conn->real_escape_string($status)."'";
if ($data) $sql .= " AND DATE(data_inicio) = '".$conn->real_escape_string($data)."'";
$sql .= " ORDER BY data_inicio DESC";
$result = $conn->query($sql);

// Status possíveis (adicione mais se quiser)
$status_opcoes = ['Planejado', 'Finalizado', 'Cancelado'];
?>
<h2>Eventos</h2>
<button class="btn btn-success" onclick="carregarConteudo('novo_evento.php')">+ Novo Evento</button>

<form method="get" style="margin-top:20px;">
    <select name="status" class="form-control" style="width:200px;display:inline;">
        <option value="">Todos os status</option>
        <?php foreach ($status_opcoes as $opt): ?>
            <option value="<?= $opt ?>" <?= ($status == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="data" class="form-control" style="width:200px;display:inline;" value="<?= htmlspecialchars($data) ?>">
    <button type="submit" class="btn btn-secondary">Filtrar</button>
</form>

<table class="table table-bordered" style="margin-top:20px;">
    <thead>
        <tr>
            <th>Título</th>
            <th>Data</th>
            <th>Local</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['titulo']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['data_inicio'])) ?></td>
            <td><?= htmlspecialchars($row['local']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <a href="#" onclick="carregarConteudo('editar_evento.php?id=<?= $row['id'] ?>'); return false;" class="btn btn-info btn-xs">Editar</a>
                <a href="excluir_evento.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Excluir este evento?')">Excluir</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
