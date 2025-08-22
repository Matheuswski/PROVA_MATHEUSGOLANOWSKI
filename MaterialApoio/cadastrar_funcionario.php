<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão para acessar a página
// Supondo que o perfil 1 seja o administrador
if($_SESSION['perfil'] != 1) {
    //echo "<script>alert('Acesso negado. Você não tem permissão para acessar esta página.');</script>";
    echo "Acesso Negado";
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST") {
    $nome_funcionario = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone =$_POST['telefone'];
    $email = $_POST['email'];

    $sql = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email) VALUES (:nome_funcionario, :endereco, :telefone, :email)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_funcionario', $nome_funcionario);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);

    if($stmt->execute()) {
        echo "<script>alert('Funcionario cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar Funcionario. Tente novamente.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Cadastrar Funcionario</title>
</head>
<body>
    <h2>Cadastrar Usuário</h2>
    <form action="cadastrar_funcionario.php" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome_funcionario" name="nome_funcionario " required><br><br>

        <label for="endereco">Endereco:</label>
        <input type="text" id="endereco" name="endereco" required><br><br>

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" required><br><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required><br><br>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <p><a href="principal.php"></a></p>

            
</body>
</html>