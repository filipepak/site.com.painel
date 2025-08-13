<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* ====== CONFIGURÁVEIS ====== */
$userName      = $_SESSION['nome'] ?? 'Usuário';
$userAvatarUrl = $_SESSION['avatar'] ?? '';                   // pode ser vazio para cair no fallback
$orgLogoUrl    = '/assets/img/logo-if.png';                   // ajuste o caminho
$orgName       = 'Instituto Federal';                         // usado no alt

/* ====== HELPERS ====== */
function formatarNomePTBR(string $nome): string {
    $nome = trim(preg_replace('/\s+/', ' ', $nome));
    $nome = mb_convert_case(mb_strtolower($nome, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    $minusculas = ['da','de','do','das','dos','di','du','e','a','o','as','os','d’','d\''];
    $partes = explode(' ', $nome);
    foreach ($partes as $i => $p) {
        $pMin = mb_strtolower($p, 'UTF-8');
        if ($i > 0 && in_array($pMin, $minusculas, true)) $partes[$i] = $pMin;
    }
    return implode(' ', $partes);
}
function cumprimentoHorario(): string {
    date_default_timezone_set('America/Sao_Paulo'); // ajuste se necessário
    $h = (int)date('H');
    if ($h < 12)   return 'Bom dia';
    if ($h < 18)   return 'Boa tarde';
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

$nomeFmt = formatarNomePTBR($userName);
$ini     = iniciaisNome($nomeFmt);
$cumpr   = cumprimentoHorario();
?>
<header class="header">
  <div class="header__left">
    <div class="header__brand">
      <img class="brand-img" src="<?= htmlspecialchars($orgLogoUrl) ?>" alt="<?= htmlspecialchars($orgName) ?>" decoding="async" />
      <span class="brand-title">Painel Administrativo</span>
    </div>
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

      <button class="user__menu-btn" type="button" aria-haspopup="menu" aria-expanded="false">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M7 10l5 5 5-5H7z"/>
        </svg>
      </button>

      <div class="user__menu" role="menu">
        <a href="perfil.php" role="menuitem">Meu perfil</a>
        <a href="config.php" role="menuitem">Configurações</a>
        <a href="logout.php" class="danger" role="menuitem">Sair</a>
      </div>
    </div>
  </div>
</header>

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

  // Dropdown usuário (fecha fora)
  (function(){
    const user = document.querySelector('.user');
    if(!user) return;
    const btn  = user.querySelector('.user__menu-btn');
    const menu = user.querySelector('.user__menu');
    function toggle(open){
      menu.classList.toggle('open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    btn.addEventListener('click', (e)=>{ e.stopPropagation(); toggle(!menu.classList.contains('open'));});
    document.addEventListener('click', ()=> toggle(false));
    menu.addEventListener('click', (e)=> e.stopPropagation());
  })();
</script>
