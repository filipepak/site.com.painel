<?php
require_once("../conexao.php");

// Carrossel (banners)
$banners = [];
$res = $conn->query("SELECT * FROM carrossel ORDER BY id DESC LIMIT 5");
while ($row = $res->fetch_assoc()) $banners[] = $row;

// Galeria de Fotos (4 últimas)
$fotos = [];
$res = $conn->query("SELECT * FROM galeria_fotos ORDER BY data_upload DESC LIMIT 4");
while ($row = $res->fetch_assoc()) $fotos[] = $row;

// Destaques (contagens)
$artigos = $conn->query("SELECT COUNT(*) as total FROM artigos")->fetch_assoc()['total'] ?? 0;
$eventos = $conn->query("SELECT COUNT(*) as total FROM eventos")->fetch_assoc()['total'] ?? 0;
// Exemplo: atletas (se tiver tabela)
$atletas = $conn->query("SHOW TABLES LIKE 'atletas'")->num_rows ? ($conn->query("SELECT COUNT(*) as total FROM atletas")->fetch_assoc()['total'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Principal - Portal Futebol Feminino</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
        body { background: #fafbfc; font-family: 'Montserrat', Arial, sans-serif;}
        .navbar-site { background: #1cc427; padding: 0 24px; display: flex; align-items: center; height: 64px;}
        .navbar-site .logo img { height: 48px; }
        .menu-links { display: flex; gap: 28px; list-style: none; margin: 0 0 0 auto; padding: 0; }
        .menu-links li a { color: #fff; font-size: 18px; font-weight: 500; text-decoration: none; transition: .2s;}
        .menu-links li a.active, .menu-links li a:hover { color: #3517be; border-bottom: 2px solid #3517be;}
        .carousel { margin: 40px auto 32px auto; max-width: 900px; border-radius: 14px; overflow: hidden; box-shadow:0 2px 16px #0002;}
        .carousel-caption { background:rgba(0,0,0,0.6); color:#fff; left:0; right:0; bottom:0; padding:18px 32px; border-radius:0 0 14px 14px; }
        .carousel-inner img { width:100%; object-fit:cover; min-height:260px; max-height:420px;}
        .destaques { max-width: 1100px; margin: 40px auto; display: flex; gap:30px; flex-wrap:wrap;}
        .destaque-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px #0001; flex:1 1 200px; min-width:220px; padding:30px; text-align:center;}
        .destaque-card h4 { color:#872ed6; font-size:1.6em;}
        .destaque-card span { font-size:2.3em; font-weight:bold; display:block; margin-bottom:8px;}
        .galeria-fotos { max-width:1100px; margin:48px auto;}
        .galeria-fotos h2 { color:#872ed6; }
        .fotos-grid { display:flex; gap:18px; flex-wrap:wrap;}
        .fotos-grid img { width:240px; height:170px; object-fit:cover; border-radius:8px; background:#eee;}
        .btn-ver { display:block; margin:18px auto 0 auto; background:#247df2; color:#fff; padding:10px 24px; border-radius:6px; border:none;}
        .rodape { background:#363c40; color:#fff; padding:48px 20px 20px 20px; display:flex; gap:32px; flex-wrap:wrap; justify-content:space-between; margin-top:40px;}
        .rodape-col { flex:1 1 240px; min-width:220px; }
        .rodape-col h3 { color:#fff; margin-top:0;}
        .rodape-col ul { padding-left:0; list-style:none;}
        .rodape-col ul li { margin-bottom:6px;}
        .rodape-col ul a { color:#fff;}
        .rodape-col input[type="email"] { width:80%; padding:6px; border-radius:5px; border:none; margin-bottom:8px;}
        .rodape-col button { padding:7px 18px; background:#872ed6; color:#fff; border:none; border-radius:4px; cursor:pointer;}
        .social-icons a img { width:30px; margin-right:8px;}
        @media (max-width:900px) {
            .destaques, .fotos-grid, .rodape { flex-direction: column; align-items: center;}
            .carousel { max-width:99vw;}
        }
    </style>
</head>
<body>
    <!-- Menu -->
    <nav class="navbar-site">
        <div class="logo">
     <img src="/portal/site/img/ifpa.JPEG" alt="Logo IFPA">

        </div>
        <ul class="menu-links">
            <li><a href="index.php" class="active">Página Principal</a></li>
            <li><a href="producao.php">Produção Acadêmica</a></li>
            <li><a href="eventos.php">Eventos</a></li>
            <li><a href="mapa.php">Mapa</a></li>
            <li><a href="info.php">Informações</a></li>
        </ul>
    </nav>

    <!-- Carrossel -->
    <div id="carouselSite" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php if (count($banners)): ?>
                <?php foreach ($banners as $i => $banner): ?>
                    <div class="item <?= $i == 0 ? 'active' : '' ?>">
                        <img src="../<?= htmlspecialchars($banner['imagem']) ?>" alt="<?= htmlspecialchars($banner['titulo']) ?>">
                        <div class="carousel-caption">
                            <h3><?= htmlspecialchars($banner['titulo']) ?></h3>
                            <p><?= htmlspecialchars($banner['descricao']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="item active">
                    <img src="img/banner1.jpg" alt="Bem-vindo">
                    <div class="carousel-caption">
                        <h3>Bem-vindo ao Portal do Futebol Feminino</h3>
                        <p>Conheça a história e conquistas das nossas atletas!</p>
                    </div>
                </div>
                <div class="item">
                    <img src="img/banner2.jpg" alt="Evento Destaque">
                    <div class="carousel-caption">
                        <h3>Evento Destaque</h3>
                        <p>Confira os próximos eventos do futebol feminino no Brasil!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <a class="left carousel-control" href="#carouselSite" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href="#carouselSite" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
    </div>

    <!-- Destaques -->
    <section class="destaques">
        <div class="destaque-card">
            <span><?= $atletas ?></span>
            <h4>Atletas</h4>
            <p>Conheça as atletas que fazem história!</p>
            <a href="atletas.php" class="btn btn-secondary">Ver Perfis</a>
        </div>
        <div class="destaque-card">
            <span><?= $artigos ?></span>
            <h4>Artigos</h4>
            <p>Artigos, pesquisas e histórias do futebol feminino.</p>
            <a href="artigos.php" class="btn btn-secondary">Ler Artigos</a>
        </div>
        <div class="destaque-card">
            <span><?= $eventos ?></span>
            <h4>Eventos</h4>
            <p>Fique por dentro dos próximos jogos e eventos.</p>
            <a href="eventos.php" class="btn btn-secondary">Ver Eventos</a>
        </div>
    </section>

    <!-- Galeria de Fotos -->
    <section class="galeria-fotos">
        <h2>Galeria de Fotos</h2>
        <div class="fotos-grid">
            <?php foreach ($fotos as $foto): ?>
                <img src="../<?= htmlspecialchars($foto['url']) ?>" alt="<?= htmlspecialchars($foto['descricao']) ?>">
            <?php endforeach; ?>
        </div>
        <a href="galeria_completa.php" class="btn-ver">Ver galeria completa</a>
    </section>

    <!-- Rodapé -->
    <footer class="rodape">
        <div class="rodape-col">
            <h3>Sobre Nós</h3>
            <p>O Portal de Futebol Feminino é dedicado a promover e divulgar o futebol praticado por mulheres no Brasil e no mundo.</p>
            <div class="social-icons">
                <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
                <a href="#"><img src="img/instagram.png" alt="Instagram"></a>
                <a href="#"><img src="img/youtube.png" alt="Youtube"></a>
            </div>
        </div>
        <div class="rodape-col">
            <h3>Links Rápidos</h3>
            <ul>
                <li><a href="index.php">Página Principal</a></li>
                <li><a href="artigos.php">Artigos</a></li>
                <li><a href="eventos.php">Eventos</a></li>
                <li><a href="info.php">Informações Gerais</a></li>
                <li><a href="contato.php">Contato</a></li>
                <li><a href="../painel.php">Painel Administrativo</a></li>
            </ul>
        </div>
        
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
