<?php
require 'conexao.php';

$msg = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $palavra = trim($_POST['palavra_chave'] ?? '');
    $nova_senha = $_POST['nova_senha'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND palavra_chave = ?");
    $stmt->bind_param("ss", $email, $palavra);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $id = $res->fetch_assoc()['id'];
        $stmt2 = $conn->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
        $stmt2->bind_param("si", $hash, $id);
        if ($stmt2->execute()) {
            $msg = "Senha alterada com sucesso! <a href='login.php'>Ir para Login</a>";
        } else {
            $erro = "Erro ao atualizar senha!";
        }
    } else {
        $erro = "Dados inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha | Portal Futebol Feminino</title>
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
        <h3>Recuperação de Senha</h3>
        <?php if ($erro): ?>
            <div class="msg-erro"><?= htmlspecialchars($erro) ?></div>
        <?php elseif ($msg): ?>
            <div class="msg-sucesso"><?= $msg ?></div>
        <?php endif; ?>
        <form method="post" style="margin-top:18px;">
            <input class="form-control" type="email" name="email" placeholder="E-mail cadastrado" required><br>
            <input class="form-control" type="text" name="palavra_chave" placeholder="Palavra-chave de recuperação" required><br>
            <input class="form-control" type="password" name="nova_senha" placeholder="Nova senha" required><br>
            <button class="btn btn-primary btn-block" type="submit">Recuperar senha</button>
        </form>
        <div style="margin-top:10px;">
            <a href="login.php">Voltar ao login</a>
        </div>
    </div>
</body>
</html>
