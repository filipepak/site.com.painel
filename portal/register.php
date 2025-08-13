<?php
require 'conexao.php';

$msg = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $palavra_chave = trim($_POST['palavra_chave'] ?? '');

    if (!$nome || !$email || !$senha || !$palavra_chave) {
        $erro = 'Todos os campos são obrigatórios!';
    } else {
        // Verifica se já existe e-mail
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $erro = 'E-mail já cadastrado!';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha_hash, palavra_chave) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $hash, $palavra_chave);

            if ($stmt->execute()) {
                header('Location: login.php?sucesso=1');
                exit;
            } else {
                $erro = "Erro ao cadastrar: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro | Portal Futebol Feminino</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .login-box { margin: 40px auto; max-width: 400px; background: #fff; padding: 30px 30px 20px; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,.14); text-align: center; }
        .logo { margin-bottom: 25px; }
        .logo img { width: 140px; }
        .msg-erro { color: #d9534f; font-weight: bold; margin: 12px 0 0 0;}
        .msg-sucesso { color: #3c763d; font-weight: bold; margin: 12px 0 0 0;}
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="ifpa.JPEG" alt="Logo do Portal">
        </div>
        <h3>Cadastro de Novo Admin</h3>
        <?php if ($erro): ?>
            <div class="msg-erro"><?= htmlspecialchars($erro) ?>
                <?php if ($erro == 'E-mail já cadastrado!'): ?>
                    <a href="login.php" class="btn btn-link">Ir para login</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <form method="post" style="margin-top:18px;">
            <input class="form-control" type="text" name="nome" placeholder="Nome completo" required><br>
            <input class="form-control" type="email" name="email" placeholder="E-mail" required><br>
            <input class="form-control" type="password" name="senha" placeholder="Senha" required><br>
            <input class="form-control" type="text" name="palavra_chave" placeholder="Palavra-chave de recuperação" required><br>
            <button class="btn btn-primary btn-block" type="submit">Cadastrar</button>
        </form>
        <div style="margin-top:10px;">
            <a href="login.php">Já tenho conta</a>
        </div>
    </div>
</body>
</html>
