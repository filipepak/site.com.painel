<?php
require 'conexao.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Controle de tentativas
if (!isset($_SESSION['tentativas_login'])) $_SESSION['tentativas_login'] = 0;

$erro = '';
$bloqueado = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = $conn->prepare("SELECT id, nome, senha_hash FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($senha, $user['senha_hash'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nome'] = $user['nome'];
            $_SESSION['tentativas_login'] = 0;
            header('Location: painel.php');
            exit;
        } else {
            $_SESSION['tentativas_login']++;
            $erro = "Senha incorreta!";
        }
    } else {
        $_SESSION['tentativas_login']++;
        $erro = "E-mail não cadastrado!";
    }
    if ($_SESSION['tentativas_login'] >= 3) $bloqueado = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login | Portal Futebol Feminino</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; }
        .login-box {
            margin: 40px auto; max-width: 360px; background: #fff; padding: 30px 30px 20px; border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,.14);
            text-align: center;
        }
        .logo { margin-bottom: 25px; }
        .logo img { width: 140px; }
        .msg-erro { color: #d9534f; font-weight: bold; margin: 12px 0 0 0;}
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <img src="ifpa.JPEG" alt="Logo do Portal">
        </div>
        <h3>Painel Administrativo</h3>
        <?php if ($erro): ?>
            <div class="msg-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($bloqueado): ?>
            <div class="alert alert-warning" style="margin-top:15px;">
                Você tentou acessar 3 vezes. <br>
                <a href="recupera_senha.php">Redefinir senha</a>
            </div>
        <?php else: ?>
            <form method="post" style="margin-top:18px;">
                <input class="form-control" type="email" name="email" placeholder="E-mail" required autofocus><br>
                <input class="form-control" type="password" name="senha" placeholder="Senha" required><br>
                <button class="btn btn-primary btn-block" type="submit">Entrar</button>
            </form>
            <div style="margin-top:10px;">
                <a href="recupera_senha.php">Esqueceu a senha?</a> | 
                <a href="register.php">Criar conta</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
