<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel Admin | Portal Futebol Feminino</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap 3 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

  <style>
    /* ======================
       VARS (tema claro base)
       ====================== */
    :root{
      --bg:#f4f6f9;
      --card:#ffffff;
      --text:#1f2937;
      --muted:#6b7280;
      --line:#e5e7eb;
      --hover:#f0f3f7;
      --active:#15c28c;
      --active-text:#0b140f;
      --shadow: 0 10px 30px rgba(0,0,0,.06);
    }
    /* tema escuro */
    .dark {
      --bg:#0f1216;
      --card:#141923;
      --text:#e5e7eb;
      --muted:#a3a3a3;
      --line:#1f2430;
      --hover:#18202b;
      --active:#15c28c;
      --active-text:#0b140f;
      --shadow: 0 10px 30px rgba(0,0,0,.45);
    }

    /* ======================
       LAYOUT BÁSICO
       ====================== */
    html,body{height:100%;}
    body{
      background:var(--bg);
      color:var(--text);
      font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial;
    }

    /* Topbar */
    .topbar{
      background:var(--card);
      border-bottom:1px solid var(--line);
      height:auto;
      box-shadow: var(--shadow);
    }
    .topbar .wrap{
      display:flex; align-items:center; justify-content:space-between;
      padding:12px 16px;
    }
    .brand{
      display:flex; align-items:center; gap:10px;
      font-weight:700; font-size:18px;
      color:var(--text);
      border-right:1px solid var(--line);
      padding-right:12px; margin-right:12px;
    }
    .btn-toggle{
      border:1px solid var(--line);
      background:transparent;
      color:var(--text);
      padding:6px 10px;
      border-radius:8px;
      transition:.2s;
    }
    .btn-toggle:hover{ background:var(--hover); }
    .right{ display:flex; align-items:center; gap:12px; }
    .logo-direita{ height:40px; border-radius:6px; }

    /* Grid (sidebar + conteúdo) */
    .container-fluid-nogap{ padding:0 10px 16px; }
    .row-nogap{ margin:0; }

    /* Sidebar */
    .sidebar{
      min-height: calc(100vh - 70px);
      background: var(--card);
      border-right:1px solid var(--line);
      padding:0;
      position:relative;
    }
    .sidebar .section-title{
      padding:12px 16px;
      font-size:12px; text-transform:uppercase;
      color:var(--muted);
      letter-spacing:.08em;
      border-bottom:1px solid var(--line);
    }
    .nav{ padding:6px; }
    .nav a{
      display:block; padding:10px 12px;
      color:var(--text); text-decoration:none;
      border-radius:8px;
      margin:4px 6px;
      transition:.15s;
    }
    .nav a:hover{ background:var(--hover); }
    .nav a.menu-link.active{
      background:var(--active);
      color:var(--active-text) !important;
    }
    .nav .collapse a{
      border-radius:8px;
      margin-left:8px;
      font-size:14.5px;
    }
    .nav a[aria-expanded]{ font-weight:600; }

    /* Conteúdo */
    .main-content{
      background:var(--card);
      min-height:600px;
      padding:24px;
      border-left:1px solid var(--line);
      box-shadow: var(--shadow);
      border-radius:10px;
      margin-top:12px;
    }

    /* Loader central */
    .loader-box{
      padding:60px; text-align:center;
    }

    /* --------- Colapso da sidebar --------- */
    /* Quando colapsada: esconde a coluna e expande a main para 12 cols */
    @media (min-width: 768px){
      body.sidebar-collapsed #sidebarCol{ display:none; }
      body.sidebar-collapsed #mainCol{ width:100%; float:none; }
    }
    /* Em telas pequenas, a sidebar vira offcanvas simples */
    @media (max-width: 767px){
      .sidebar{
        position:fixed; z-index:1030; top:70px; bottom:0; left:-260px;
        width:240px; transition:left .25s ease;
        box-shadow: var(--shadow);
      }
      .sidebar.is-open{ left:0; }
      #mainCol{ width:100%; float:none; }
    }
  </style>
</head>
<body>
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="wrap">
      <div class="brand">
        <button id="toggleMenu" class="btn-toggle" title="Mostrar/ocultar menu">☰ Menu</button>
        <button id="toggleTheme" class="btn-toggle" title="Alternar tema">🌙 Tema</button>
        <span>Painel Administrativo</span>
      </div>
      <div class="right">
        <span>Bem-vindo, <b><?= htmlspecialchars($_SESSION['admin_nome']) ?></b></span>
        <img class="logo-direita" src="ifpa.JPEG" alt="IFPA Logo">
      </div>
    </div>
  </div>

  <!-- GRID -->
  <div class="container-fluid container-fluid-nogap">
    <div class="row row-nogap" style="margin-top:10px;">
      <!-- SIDEBAR -->
      <div id="sidebarCol" class="col-sm-2 sidebar">
        <div class="section-title">Menu</div>
        <nav class="nav">
          <a href="dashboard_conteudo.php" class="menu-link active">Dashboard</a>

          <!-- Grupo: Conteúdos -->
          <a href="#grp-conteudos" data-toggle="collapse" aria-expanded="true">▾ Conteúdos</a>
          <div id="grp-conteudos" class="collapse in">
            <a href="artigos.php" class="menu-link">Artigos</a>
            <a href="noticias.php" class="menu-link">Notícias</a>
            <a href="galeria.php" class="menu-link">Galeria</a>
            <a href="carrossel.php" class="menu-link">Carrossel</a>
            <a href="eventos.php" class="menu-link">Eventos</a>
          </div>

          <!-- Mapa abre modal -->
          <a href="#" id="abrirMapas">Mapas</a>

          <!-- Outro grupo opcional -->
          <a href="#grp-sistema" data-toggle="collapse" aria-expanded="true">▾ Sistema</a>
          <div id="grp-sistema" class="collapse in">
            <a href="configuracoes.php" class="menu-link">Configurações</a>
            <a href="logout.php" class="menu-link" style="color:#d9534f;font-weight:bold;">Sair</a>
          </div>
        </nav>
      </div>

      <!-- MAIN -->
      <div id="mainCol" class="col-sm-10">
        <div id="conteudo-central" class="main-content">
          <div class="loader-box">
            <img src="https://i.imgur.com/EATfJ2A.gif" alt="Carregando..." width="40">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: MAPAS -->
  <div class="modal fade" id="modalMapas" tabindex="-1" role="dialog" aria-labelledby="modalMapasLabel">
    <div class="modal-dialog modal-lg" role="document" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header" style="background:#15c28c;color:#fff;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="modalMapasLabel"><b>Mapas / Artigos</b></h4>
        </div>
        <div class="modal-body" id="modalMapasConteudo" style="padding:0;">
          <div class="loader-box"><img src="https://i.imgur.com/EATfJ2A.gif" width="40" alt="Carregando..."></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Voltar ao menu</button>
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPTS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <script>
    /* ===== Tema escuro (persistência) ===== */
    (function(){
      const KEY = 'painel.theme';
      const saved = localStorage.getItem(KEY);
      if (saved === 'dark') document.body.classList.add('dark');
      document.getElementById('toggleTheme').addEventListener('click', function(){
        document.body.classList.toggle('dark');
        localStorage.setItem(KEY, document.body.classList.contains('dark') ? 'dark' : 'light');
      });
    })();

    /* ===== Mostrar/ocultar sidebar ===== */
    (function(){
      var isSmall = window.matchMedia("(max-width: 767px)");
      var $sidebar = $('#sidebarCol');
      var $mainCol = $('#mainCol');

      function collapseDesktop(){
        // desktop: esconder sidebar e expandir conteúdo pra 12 colunas
        document.body.classList.toggle('sidebar-collapsed');
      }
      function toggleMobile(){
        // mobile: sidebar vira off-canvas
        $sidebar.toggleClass('is-open');
      }

      $('#toggleMenu').on('click', function(){
        if (isSmall.matches) toggleMobile();
        else collapseDesktop();
      });

      // Ao clicar em qualquer link da sidebar no mobile, fecha o off-canvas
      $('.sidebar a').on('click', function(){
        if (isSmall.matches) $sidebar.removeClass('is-open');
      });
    })();

    /* ===== Loader e AJAX de conteúdo ===== */
    function carregarConteudo(url) {
      $("#conteudo-central").html('<div class="loader-box"><img src="https://i.imgur.com/EATfJ2A.gif" alt="Carregando..." width="40"></div>');
      $("#conteudo-central").load(url, function(resp, status){
        if (status === "error") {
          $("#conteudo-central").html("<div class='alert alert-danger'>Erro ao carregar conteúdo.</div>");
        }
      });
    }

    // Clique nos itens do menu principal
    $(document).on('click', '.menu-link', function(e) {
      var href = $(this).attr('href');
      if (href === "logout.php") return; // deixa o logout normal

      e.preventDefault();
      // ativa visual apenas nos itens clicáveis
      $('.menu-link').removeClass('active');
      $(this).addClass('active');

      carregarConteudo(href);
    });

    // Carrega o Dashboard ao abrir
    $(function() {
      carregarConteudo('dashboard_conteudo.php');
    });

    /* ===== MAPAS NO MODAL ===== */
    $('#abrirMapas').click(function(e){
      e.preventDefault();
      $('#modalMapas').modal('show');
      $('#modalMapasConteudo').html('<div class="loader-box"><img src="https://i.imgur.com/EATfJ2A.gif" width="40" alt="Carregando..."></div>');
      $('#modalMapasConteudo').load('mapas_listar.php', function(resp, status){
        if (status === "error") {
          $('#modalMapasConteudo').html("<div class='alert alert-danger'>Erro ao carregar mapas.</div>");
        }
      });
    });
  </script>
</body>
</html>
