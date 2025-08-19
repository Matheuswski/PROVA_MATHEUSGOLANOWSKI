<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes_email.php'; // Arquivo com as funções que geram a senha e enviam o email

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Verifica se o email existe no banco de dados
    $sql = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Gera uma nova senha temporária
        $senha_temporaria = gerarSenhaTemporaria();
        $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

        // Atualiza a senha no banco de dados
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = 1 WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":senha", $senha_hash);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        // Simula o envio de email
        simularEnvioEmail($email, $senha_temporaria);
        echo "<script>alert('Uma senha temporaria foi enviada para o seu email (simulação). Verifique o arquivo emails_simulados.txt'); window.location.href = 'login.php';</script>";

    } else {
        echo "<script>alert('Email não encontrado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Recuperar Senha</h2>
    <form action="recuperar_senha.php" method="post">
        <label for="email">Digite o seu email cadastrado:</label>
        <input type="email" id="email" name="email" required><br>

        <button type="submit">Enviar senha temporaria</button>
</body>
</html>
</html>