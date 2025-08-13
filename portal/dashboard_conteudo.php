<?php
// ================== BOOT ==================
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_id'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(401);
        echo "<div class='alert alert-danger'>Sessão expirada. Faça login novamente.</div>";
        exit;
    } else {
        header('Location: login.php'); exit;
    }
}
require 'conexao.php';

// ================== HELPERS ==================
function formatarNomePTBR(string $nome): string {
    $nome = trim(preg_replace('/\s+/', ' ', $nome));
    $nome = mb_convert_case(mb_strtolower($nome, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    $minusculas = ['da','de','do','das','dos','di','du','e','a','o','as','os','d’',"d'"];
    $partes = explode(' ', $nome);
    foreach ($partes as $i => $p) {
        $pMin = mb_strtolower($p, 'UTF-8');
        if ($i > 0 && in_array($pMin, $minusculas, true)) $partes[$i] = $pMin;
    }
    return implode(' ', $partes);
}
function cumprimentoHorario(): string {
    date_default_timezone_set('America/Sao_Paulo');
    $h = (int)date('H');
    if ($h < 12) return 'Bom dia';
    if ($h < 18) return 'Boa tarde';
    return 'Boa noite';
}
function iniciaisNome(string $nome): string {
    $nome = trim($nome);
    if ($nome === '') return 'U';
    $p = preg_split('/\s+/u', $nome);
    $ini = mb_strtoupper(mb_substr($p[0],0,1,'UTF-8'),'UTF-8');
    if (count($p) > 1) $ini .= mb_strtoupper(mb_substr(end($p),0,1,'UTF-8'),'UTF-8');
    return $ini;
}
function getCount($conn, $tabela) {
    $res = $conn->query("SELECT COUNT(*) as total FROM $tabela");
    if ($res && $row = $res->fetch_assoc()) return (int)$row['total'];
    return 0;
}

// ================== DADOS ==================
$totalArtigos  = getCount($conn, 'artigos');
$totalEventos  = getCount($conn, 'eventos');
$totalNoticias = getCount($conn, 'noticias');
$totalFotos    = getCount($conn, 'galeria_fotos');

$atividades = [];
$artigos = $conn->query("SELECT 'artigo' AS tipo, id, titulo, data_publicacao AS data FROM artigos ORDER BY data_publicacao DESC LIMIT 5");
$eventos = $conn->query("SELECT 'evento' AS tipo, id, titulo, data_inicio AS data FROM eventos ORDER BY data_inicio DESC LIMIT 5");
if ($artigos) while($row = $artigos->fetch_assoc()) $atividades[] = $row;
if ($eventos) while($row = $eventos->fetch_assoc()) $atividades[] = $row;
usort($atividades, fn($a,$b)=> strtotime($b['data']) - strtotime($a['data']));
$atividades = array_slice($atividades, 0, 5);

// Usuário/logos
$nomeBruto       = $_SESSION['nome'] ?? 'Usuário';
$nomeFmt         = formatarNomePTBR($nomeBruto);
$ini             = iniciaisNome($nomeFmt);
$cumpr           = cumprimentoHorario();
$userAvatarUrl   = $_SESSION['avatar'] ?? '';
$orgLogoUrl      = '/assets/img/logo-if.png'; // <<< AJUSTE O CAMINHO
$orgName         = 'Instituto Federal';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel Administrativo</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f6f8fb; --bg-soft:#f3f6f8; --surface:#fff; --text:#1f2d3d; --muted:#6b7a90;
  --primary:#15c28c; --primary-600:#11a777; --primary-100:#e6fbf4; --accent:#122252;
  --border:#e7eef3; --shadow:0 2px 12px rgba(17,167,119,.10);
  --radius:14px; --radius-sm:10px; --radius-xs:8px;
}
*{box-sizing:border-box} html,body{height:100%}
body{margin:0;background:var(--bg);color:var(--text);font:400 14px/1.45 "Inter",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}
.theme-dark{--bg:#0f141a;--bg-soft:#0b1016;--surface:#121922;--text:#e7edf3;--muted:#a4b0be;--border:#1d2835;--shadow:0 6px 18px rgba(0,0,0,.35);--accent:#b9c6ff}
.wrapper{display:grid;grid-template-columns:240px 1fr;grid-template-rows:64px 1fr;grid-template-areas:"header header" "sidebar content";min-height:100vh}

/* Header */
.header{grid-area:header;background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;display:flex;align-items:center;justify-content:space-between;height:64px;padding:0 16px}
.header__brand{display:flex;align-items:center;gap:10px}
.brand-img{height:36px;width:auto;object-fit:contain;display:block}
.brand-title{font-weight:800;letter-spacing:.2px;color:var(--accent);white-space:nowrap;font-size:16px}
.header__right{display:flex;align-items:center;gap:14px}
.welcome{white-space:nowrap;line-height:1}
.welcome-sub{color:#5f6b7a;margin-right:4px}
.welcome-name{color:var(--accent);font-weight:800}
.btn.btn-ghost{background:transparent;color:#3a4756;border:1px solid rgba(0,0,0,.08);padding:8px 10px;border-radius:8px;cursor:pointer;transition:.16s}
.btn.btn-ghost:hover{background:rgba(0,0,0,.06)}
.user{position:relative;display:flex;align-items:center;gap:6px}
.avatar{height:36px;width:36px;border-radius:50%;object-fit:cover;display:block;border:1px solid var(--border);background:#fff}
.avatar--fallback{display:inline-flex;align-items:center;justify-content:center;height:36px;width:36px;border-radius:50%;background:var(--primary-100);color:var(--primary-600);font-weight:800;border:1px solid var(--border)}
.user__menu-btn{border:none;background:transparent;cursor:pointer;padding:6px;color:#6b7a90;border-radius:8px}
.user__menu-btn:hover{background:rgba(0,0,0,.05)}
.user__menu{position:absolute;top:100%;right:0;margin-top:8px;min-width:180px;background:var(--surface);border:1px solid var(--border);border-radius:10px;box-shadow:var(--shadow);padding:6px;display:none;overflow:hidden}
.user__menu.open{display:block}
.user__menu a{display:block;padding:10px 12px;border-radius:8px;color:var(--text);text-decoration:none}
.user__menu a:hover{background:var(--bg)}
.user__menu a.danger{color:#e74c3c}

/* Sidebar */
.sidebar{grid-area:sidebar;background:var(--surface);border-right:1px solid var(--border);padding:10px 0;position:sticky;top:64px;height:calc(100vh - 64px);overflow:auto}
.nav{list-style:none;margin:0;padding:0}
.nav li a{display:flex;align-items:center;gap:10px;padding:11px 16px;color:var(--text);text-decoration:none;border-left:3px solid transparent;transition:.16s}
.nav li a:hover{background:var(--bg-soft)}
.nav li a.active{background:var(--primary-100);border-left-color:var(--primary);color:var(--primary-600);font-weight:700}
.nav .danger a{color:#e74c3c}.nav .danger a:hover{background:#feefef}

/* Conteúdo */
.content{grid-area:content;padding:18px 18px 40px 18px}
.page-hero{background:linear-gradient(90deg,var(--primary) 0%,#10b07f 100%);color:#fff;border-radius:14px;padding:18px;text-align:center;margin:6px 0 22px 0;font-weight:800;letter-spacing:.3px}

/* Cards de estatística */
.stats-row{display:flex;flex-wrap:wrap;justify-content:center;gap:22px;margin:12px auto 24px;max-width:1100px}
.stat-box{background:var(--surface);border:1px solid var(--border);border-radius:14px;min-width:240px;max-width:260px;flex:1 1 220px;text-align:center;padding:26px 0 20px;box-shadow:var(--shadow);transition:transform .14s, box-shadow .14s, border-color .14s}
.stat-box:hover{transform:translateY(-3px);box-shadow:0 10px 26px rgba(21,194,140,.18);border-color:#c9f7e7}
.stat-box h3{margin:0 0 6px 0;font-size:30px;color:var(--accent);font-weight:800}
.stat-box span{color:var(--primary-600);font-weight:600}

/* Atividades recentes */
.recent-activity{margin:6px auto;max-width:980px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
.recent-activity .card{padding:18px 18px 12px}
.recent-activity h4{margin:0 0 12px;color:var(--primary-600);font-weight:800}
.activity-item{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 12px;margin-bottom:8px;display:flex;align-items:center;gap:10px}
.label{background:var(--primary-100);color:var(--primary-600);border-radius:999px;padding:2px 10px;font-weight:700;font-size:.86rem}
.activity-item b{color:var(--accent);font-weight:700}
.activity-item .date{margin-left:auto;color:#6b7a90;font-size:.9rem}
.activity-item a{margin-left:10px;color:var(--primary-600);text-decoration:none}
.activity-item a:hover{text-decoration:underline}

/* Responsivo */
@media (max-width:1024px){.wrapper{grid-template-columns:210px 1fr}}
@media (max-width:860px){
  .wrapper{grid-template-columns:1fr;grid-template-rows:64px auto auto;grid-template-areas:"header" "sidebar" "content"}
  .sidebar{position:static;height:auto}
}
@media (max-width:720px){.brand-title{display:none}.welcome-name{max-width:46vw;overflow:hidden;text-overflow:ellipsis}}
</style>
</head>
<body>
<div class="wrapper">

  <!-- HEADER -->
  <header class="header">
    <div class="header__brand">
      <img class="brand-img" src="<?= htmlspecialchars($orgLogoUrl) ?>" alt="<?= htmlspecialchars($orgName) ?>" decoding="async">
      <span class="brand-title">Painel Administrativo</span>
    </div>

    <div class="header__right">
      <div class="welcome">
        <span class="welcome-sub"><?= $cumpr ?>,</span>
        <strong class="welcome-name"><?= htmlspecialchars($nomeFmt) ?></strong>
      </div>

      <button class="btn btn-ghost theme-toggle" type="button" title="Alternar tema" aria-label="Alternar tema">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M21.64 13a1 1 0 0 0-1.05-.14A8 8 0 0 1 11.14 3.4a1 1 0 0 0-1.19-1.3A10 10 0 1 0 22 14.19a1 1 0 0 0-.36-1.19z"/>
        </svg>
      </button>

      <div class="user">
        <?php if ($userAvatarUrl): ?>
          <img class="avatar" src="<?= htmlspecialchars($userAvatarUrl) ?>" alt="Avatar de <?= htmlspecialchars($nomeFmt) ?>" referrerpolicy="no-referrer">
        <?php else: ?>
          <span class="avatar avatar--fallback" aria-hidden="true"><?= $ini ?></span>
        <?php endif; ?>

        <button class="user__menu-btn" type="button" aria-haspopup="menu" aria-expanded="false" title="Abrir menu do usuário">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 10l5 5 5-5H7z"/></svg>
        </button>
        <div class="user__menu" role="menu">
          <a href="perfil.php" role="menuitem">Meu perfil</a>
          <a href="config.php" role="menuitem">Configurações</a>
          <a href="logout.php" class="danger" role="menuitem">Sair</a>
        </div>
      </div>
    </div>
  </header>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <ul class="nav">
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <li><a href="artigos.php">Artigos</a></li>
      <li><a href="carrossel.php">Carrossel</a></li>
      <li><a href="eventos.php">Eventos</a></li>
      <li><a href="noticias.php">Notícias</a></li>
      <li><a href="mapas.php">Mapas</a></li>
      <li><a href="galeria.php">Galeria</a></li>
      <li><a href="config.php">Configurações</a></li>
      <li class="danger"><a href="logout.php">Sair</a></li>
    </ul>
  </aside>

  <!-- CONTEÚDO -->
  <main class="content">
    <div class="page-hero">Painel Administrativo</div>

    <div class="stats-row">
      <div class="stat-box"><h3><?= $totalArtigos ?></h3><span>Artigos</span></div>
      <div class="stat-box"><h3><?= $totalEventos ?></h3><span>Eventos</span></div>
      <div class="stat-box"><h3><?= $totalNoticias ?></h3><span>Notícias</span></div>
      <div class="stat-box"><h3><?= $totalFotos ?></h3><span>Fotos</span></div>
    </div>

    <section class="recent-activity">
      <div class="card">
        <h4>Atividades Recentes</h4>
        <?php if (count($atividades) === 0): ?>
          <div class="activity-item"><span class="label">Info</span> Nenhuma atividade recente.</div>
        <?php else: foreach($atividades as $item): ?>
          <div class="activity-item">
            <span class="label"><?= ucfirst($item['tipo']) ?></span>
            <b><?= htmlspecialchars($item['titulo']) ?></b>
            <span class="date"><?= $item['data'] ? date('d/m/Y', strtotime($item['data'])) : '' ?></span>
            <?php if ($item['tipo'] === 'artigo'): ?>
              <a href="editar_artigo.php?id=<?= $item['id'] ?>">Editar</a>
            <?php else: ?>
              <a href="editar_evento.php?id=<?= $item['id'] ?>">Editar</a>
            <?php endif; ?>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </section>
  </main>
</div>

<script>
// Tema persistente
(function initTheme(){
  const saved = localStorage.getItem('themeDark') === '1';
  if(saved) document.body.classList.add('theme-dark');
})();
document.querySelector('.theme-toggle')?.addEventListener('click', ()=>{
  document.body.classList.toggle('theme-dark');
  localStorage.setItem('themeDark', document.body.classList.contains('theme-dark')?'1':'0');
});

// Dropdown usuário
(function(){
  const user = document.querySelector('.user'); if(!user) return;
  const btn = user.querySelector('.user__menu-btn'); const menu = user.querySelector('.user__menu');
  function toggle(open){ menu.classList.toggle('open', open); btn.setAttribute('aria-expanded', open?'true':'false'); }
  btn.addEventListener('click', (e)=>{ e.stopPropagation(); toggle(!menu.classList.contains('open')); });
  document.addEventListener('click', ()=> toggle(false));
  menu.addEventListener('click', (e)=> e.stopPropagation());
})();
</script>
</body>
</html>
