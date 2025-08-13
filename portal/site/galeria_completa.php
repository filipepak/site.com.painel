<?php
// require_once '../conexao.php'; // Ative se quiser trazer fotos do banco
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Galeria Completa | Portal Futebol Feminino</title>
    <link rel="stylesheet" href="estilo.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'menu_topo.php'; ?> <!-- Se preferir pode colar o menu aqui direto -->

<section class="hero-banner" style="background:#872ed6;">
    <div class="hero-content">
        <h1>Galeria de Fotos</h1>
        <p>Confira os registros do futebol feminino!</p>
    </div>
</section>

<section class="galeria-fotos" style="padding-bottom:40px;">
    <h2 style="margin-bottom:30px;">Todas as Fotos</h2>
    <div class="fotos-grid">
        <!-- Exemplo estático, depois troque por um loop de fotos do banco -->
        <img src="img/foto1.jpg" alt="Foto 1">
        <img src="img/foto2.jpg" alt="Foto 2">
        <img src="img/foto3.jpg" alt="Foto 3">
        <img src="img/foto4.jpg" alt="Foto 4">
        <img src="img/foto5.jpg" alt="Foto 5">
        <img src="img/foto6.jpg" alt="Foto 6">
        <img src="img/foto7.jpg" alt="Foto 7">
        <img src="img/foto8.jpg" alt="Foto 8">
    </div>
</section>

<?php include 'rodape.php'; ?>
</body>
</html>
