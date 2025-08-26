<?php
    session_start();

    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSÃO
    // SUPONDO QUE O PERFIL '1' SEJA O 'ADM'
    if($_SESSION['perfil'] != 1){
        echo "Acesso negado!";
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $nome_funcionario = $_POST['nome_funcionario'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        
        $query = "INSERT INTO funcionario (nome_funcionario, endereco, telefone, email) VALUES (:nome_funcionario, :endereco, :telefone, :email)";

        $stmt = $pdo -> prepare($query);

        $stmt -> bindParam(":nome_funcionario", $nome);
        $stmt -> bindParam(":endereco", $endereco);
        $stmt -> bindParam(":telefone", $telefone);
        $stmt -> bindParam(":email", $email);

        try {
            $stmt -> execute();
            echo "<script> alert('Usuário cadastrado com sucesso!'); </script>";
        } catch (PDOException $e) {
            echo "<script> alert('Erro ao cadastrar o usuário! Verifique as informações inseridas.'); </script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Usuário</title>

    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h2>Cadastro Usuário</h2>

    <form action="cadastro_usuario.php" method="POST">
        <label for="nome_funcionario">Nome:</label>
        <input type="text" name="nome_funcionario" id="nome_funcionario" required onkeypress="mascara(this, somentetexto)">

        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>

        <label for="id_perfil">Perfil:</label>
        <select name="id_perfil" id="id_perfil">
            <option value="1">Administrador</option>
            <option value="2">Secretária</option>
            <option value="3">Almoxarife</option>
            <option value="4">Cliente</option>
        </select>

        <button type="submit">Cadastrar</button>
        <button type="reset">Cancelar</button>
    </form>

    <a class="btn-voltar" href="principal.php">Voltar</a>

    <script src="validacoes.js"></script>
</body>
</html>