<?php
// Lista de estados e seus respectivos arquivos PHP
$estados = [
    'AC' => 'acre.php',
    'AL' => 'alagoas.php',
    'AP' => 'amapa.php',
    'AM' => 'amazonas.php',
    'BA' => 'bahia.php',
    'CE' => 'ceara.php',
    'DF' => 'distritofederal.php',
    'ES' => 'espiritosanto.php',
    'GO' => 'goias.php',
    'MA' => 'maranhao.php',
    'MT' => 'matogrosso.php',
    'MS' => 'matogrossodosul.php',
    'MG' => 'minasgerais.php',
    'PA' => 'para.php',
    'PB' => 'paraiba.php',
    'PR' => 'parana.php',
    'PE' => 'pernambuco.php',
    'PI' => 'piaui.php',
    'RJ' => 'riodejaneiro.php',
    'RN' => 'riograndedonorte.php',
    'RS' => 'riograndedosul.php',
    'RO' => 'rondonia.php',
    'RR' => 'roraima.php',
    'SC' => 'santacatarina.php',
    'SP' => 'saopaulo.php',
    'SE' => 'sergipe.php',
    'TO' => 'tocantins.php'
];

// Exibe uma lista de links para cada estado
echo "<ul>";
foreach ($estados as $sigla => $arquivo) {
    echo "<li><a href=\"$arquivo\">$sigla</a></li>";
}
echo "</ul>";
?>
