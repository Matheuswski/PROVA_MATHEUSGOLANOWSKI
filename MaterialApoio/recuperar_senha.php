<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];

    $sql = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if($usuario) {
        $senha_temporaria = gerarSenhaTemporaria();
        $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = TRUE WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":senha", $senha_hash);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        // Envia o e-mail com a senha temporária
        simularEnvioEmail($email,$senha, );
        echo "<script>alert('Uma senha temporaria foi gerada e eviada(simulação).Verifique o arquivo emails_simulados.txt'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Email não encontrado!'); window.location.href='recuperar_senha.php';</script>";
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
    <form action="recuperar_senha.php" method="POST">
        <label for="email">Digite o seu e-mail cadastrado</label>
        <input type="email" id="email" name="email" required>
        
        <button type="submit">Enviar nova senha</button>
</body>
</html>