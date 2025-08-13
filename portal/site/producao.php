<?php 
require_once("../conexao.php");

// Filtro do tipo (artigo, dissertacao, tese)
$tipos = ['artigo' => 'Artigos', 'dissertacao' => 'Mestrados', 'tese' => 'Doutorados'];
$filtro = $_GET['tipo'] ?? 'todos';

$where = "";
if (in_array($filtro, array_keys($tipos))) {
    $where = "WHERE tipo = '$filtro'";
}

$sql = "SELECT * FROM artigos $where ORDER BY data_publicacao DESC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Produção Acadêmica</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body { background: #f7f8f9; font-family: 'Montserrat', Arial, sans-serif;}
        .filtros { margin: 32px 0 20px 0; text-align: center;}
        .filtros a {
            background: #f2f2f2; border-radius: 6px; margin-right:10px; padding:9px 32px;
            color:#872ed6; text-decoration: none; font-weight: bold; font-size:17px;
            box-shadow: 0 1px 7px #0001;
        }
        .filtros a.active, .filtros a:hover { background:#872ed6; color:#fff;}
        .cards-producao { display: flex; flex-wrap: wrap; justify-content: center; gap: 28px;}
        .card-producao {
            background: #fffbe5;
            border-radius: 15px;
            box-shadow: 0 3px 16px #0001;
            padding: 0;
            max-width: 370px;
            min-width: 320px;
            margin: 12px;
            overflow: hidden;
            text-align: center;
            border: 4px solid #fde49c;
            display: flex;
            flex-direction: column;
        }
        .card-tipo {
            background: #fea236;
            color: #fff;
            font-weight: bold;
            padding: 12px 0;
            font-size: 20px;
        }
        .card-capa {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-bottom: 2px solid #fde49c;
            background: #eee;
        }
        .card-conteudo {
            padding: 18px 16px 24px 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .card-titulo {
            font-weight: bold;
            font-size: 17px;
            margin-bottom: 8px;
            color: #222;
            min-height: 40px;
        }
        .card-autores, .card-inst {
            font-size: 15px;
            margin-bottom: 6px;
            color: #313131;
        }
        .card-data { color:#888; font-size:13px; margin-bottom: 8px;}
        .card-resumo { font-size:14px; color:#666; margin-bottom: 12px;}
        .card-botoes {
            margin-top: 10px;
        }
        .btn-artigo {
            background: #1686ef;
            color: #fff;
            border-radius: 5px;
            padding: 8px 20px;
            text-decoration: none;
            display: inline-block;
            margin: 2px 8px;
            font-weight: bold;
            border: none;
            font-size: 16px;
            transition: 0.2s;
        }
        .btn-artigo:hover {
            background: #085fad;
        }
        @media (max-width: 900px) {
            .cards-producao { flex-direction: column; align-items: center; gap: 22px;}
            .card-producao { margin: 0 auto;}
        }
    </style>
</head>
<body>
    <h1 style="color:#872ed6;margin-top:40px;text-align:center;">Produção Acadêmica</h1>

    <div class="filtros">
        <a href="?tipo=artigo" class="<?= $filtro=='artigo' ? 'active' : '' ?>">Artigos</a>
        <a href="?tipo=dissertacao" class="<?= $filtro=='dissertacao' ? 'active' : '' ?>">Mestrados</a>
        <a href="?tipo=tese" class="<?= $filtro=='tese' ? 'active' : '' ?>">Doutorados</a>
        <a href="?tipo=todos" class="<?= $filtro=='todos' ? 'active' : '' ?>">Todos</a>
    </div>

    <?php if ($res->num_rows === 0): ?>
        <div style="margin:45px 0 80px 0; text-align:center;">Nenhuma produção encontrada.</div>
    <?php else: ?>
    <div class="cards-producao">
        <?php while($row = $res->fetch_assoc()): ?>
            <div class="card-producao <?= strtolower($row['tipo']) ?>">
                <div class="card-tipo"><?= ucfirst($tipos[$row['tipo']] ?? $row['tipo']) ?></div>
                <img class="card-capa" src="<?= htmlspecialchars($row['capa'] ?: 'img/capa_padrao.jpg') ?>" alt="Capa">
                <div class="card-conteudo">
                    <div class="card-data"><?= date('d/m/Y', strtotime($row['data_publicacao'])) ?></div>
                    <div class="card-titulo"><?= htmlspecialchars($row['titulo']) ?></div>
                    <div class="card-autores"><?= htmlspecialchars($row['autores']) ?></div>
                    <?php if (!empty($row['instituicao'])): ?>
                        <div class="card-inst"><?= htmlspecialchars($row['instituicao']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($row['resumo'])): ?>
                        <div class="card-resumo"><?= nl2br(htmlspecialchars($row['resumo'])) ?></div>
                    <?php endif; ?>
                    <div class="card-botoes">
                        <?php if (!empty($row['link'])): ?>
                            <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="btn-artigo">Ver Online</a>
                        <?php endif; ?>
                        <?php if (!empty($row['arquivo'])): ?>
                            <a href="../uploads/<?= htmlspecialchars($row['arquivo']) ?>" class="btn-artigo" download>Baixar PDF</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</body>
</html>
