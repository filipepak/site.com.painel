<?php  
include_once('../conexao.php');

// 1. TROQUE o nome da sigla abaixo para a sigla correta deste estado
$estado = 'MA'; // Exemplo: 'PA', 'TO', 'AM', etc.

// 2. Filtros recebidos
$tipos_validos = ['artigo', 'mestrado', 'doutorado'];
$tipo    = isset($_GET['tipo'])   && in_array($_GET['tipo'], $tipos_validos) ? $_GET['tipo'] : '';
$palavra = isset($_GET['busca'])  ? trim($_GET['busca']) : '';
$ano     = isset($_GET['ano'])    ? trim($_GET['ano'])   : '';

// 3. Montagem da query dinâmica e segura
$sql    = "SELECT * FROM mapas WHERE estado = ?";
$params = [$estado];
$tipos  = "s";

if ($tipo) {
    $sql .= " AND tipo = ?";
    $params[] = $tipo;
    $tipos   .= "s";
}
if ($palavra) {
    $sql .= " AND (titulo LIKE ? OR descricao LIKE ?)";
    $busca = "%$palavra%";
    $params[] = $busca;
    $params[] = $busca;
    $tipos   .= "ss";
}
if ($ano) {
    $sql .= " AND ano_publicacao = ?";
    $params[] = $ano;
    $tipos   .= "s";
}
$sql .= " ORDER BY ano_publicacao DESC, titulo ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(ucfirst($estado)) ?> - Pesquisas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="estados.css">
    <link rel="stylesheet" href="cards.css">
    <style>
        .container {
            max-width: 1080px;
            margin: 36px auto 0 auto;
            padding: 0 18px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
            gap: 28px;
            justify-items: center;
            margin-top: 32px;
            width: 100%;
        }
        @media (min-width: 1400px) {
            .grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
    </style>
</head>
<body>
<?php include '../site/menu_topo.php'; ?>

<div class="container">
    <h1 style="text-align:center; margin-bottom:22px; font-size:2.1em;">
        <?= htmlspecialchars(ucfirst($estado)) ?> - Pesquisas Publicadas
    </h1>

    <!-- FILTROS -->
    <form method="get" class="filtros" autocomplete="off" style="display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-bottom:32px;">
        <select name="tipo" aria-label="Filtrar por tipo">
            <option value="">Todos os tipos</option>
            <option value="artigo"    <?= $tipo=='artigo'    ?'selected':'' ?>>Artigo</option>
            <option value="mestrado"  <?= $tipo=='mestrado'  ?'selected':'' ?>>Mestrado</option>
            <option value="doutorado" <?= $tipo=='doutorado' ?'selected':'' ?>>Doutorado</option>
        </select>
        <input type="text" name="busca" placeholder="Palavra-chave" value="<?= htmlspecialchars($palavra) ?>" maxlength="100" aria-label="Pesquisar por palavra">
        <input type="number" name="ano" placeholder="Ano" min="1900" max="2099" value="<?= htmlspecialchars($ano) ?>" aria-label="Filtrar por ano">
        <button type="submit">Filtrar</button>
        <?php if($tipo || $palavra || $ano): ?>
            <a href="<?= strtok($_SERVER["REQUEST_URI"],'?') ?>" style="margin-left:10px;color:#999;font-size:.95em;align-self:center;">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="grid">
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card-mapa <?= strtolower($row['tipo']) ?>">
            <div class="card-topo <?= strtolower($row['tipo']) ?>">
                <?= ucfirst($row['tipo']) ?>
            </div>
            <?php if (!empty($row['imagem'])): ?>
                <div class="card-img">
                    <img src="../uploads/<?= htmlspecialchars($row['imagem']) ?>" alt="Capa do artigo">
                </div>
            <?php else: ?>
                <div class="card-img card-img-placeholder"></div>
            <?php endif; ?>
            <div class="card-body">
                <div class="card-label">Título</div>
                <div class="card-info"><?= htmlspecialchars($row['titulo']) ?></div>
                <div class="card-label">Autores</div>
                <div class="card-info"><?= htmlspecialchars($row['autores']) ?></div>
                <div class="card-label">Ano</div>
                <div class="card-info"><?= htmlspecialchars($row['ano_publicacao']) ?></div>
                <div class="card-label">Instituto</div>
                <div class="card-info"><?= htmlspecialchars($row['instituicao']) ?></div>
                <?php if (!empty($row['arquivo'])): ?>
                    <a class="card-btn" href="../uploads/<?= rawurlencode($row['arquivo']) ?>" target="_blank">ler artigo</a>
                <?php elseif (!empty($row['link'])): ?>
                    <a class="card-btn" href="<?= htmlspecialchars($row['link']) ?>" target="_blank">ler artigo</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
    <?php if ($result->num_rows == 0): ?>
        <div style="grid-column: 1 / -1; color:#a22; text-align:center;">Nenhum resultado encontrado.</div>
    <?php endif; ?>
    </div>
</div>
<?php include '../site/rodape.php'; ?>
</body>
</html>
