<?php
header('Content-Type: application/json');

// Configurações do banco
$host = "localhost";
$user = "root";
$pass = ""; // coloque sua senha se tiver
$db   = "portal_futebol";

// Conexão
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão com o banco de dados.']);
    exit;
}

// Consulta SQL
$sql = "SELECT id, nome, latitude, longitude, descricao, criado_em FROM pontos_mapa";
$result = $conn->query($sql);

$pontos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pontos[] = [
            "id"        => $row["id"],
            "nome"      => $row["nome"],
            "latitude"  => (float)$row["latitude"],
            "longitude" => (float)$row["longitude"],
            "descricao" => $row["descricao"],
            "criado_em" => $row["criado_em"]
        ];
    }
}

echo json_encode($pontos);

$conn->close();
?>
