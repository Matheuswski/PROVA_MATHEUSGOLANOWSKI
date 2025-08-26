<?php
    session_start();

    require_once 'conexao.php';

    //GARANTE QUE O USUARIO ESTEJA LOGADO
    if(!isset($_SESSION['id_usuario'])){
        echo "<script> alert('Acesso negado!'); window.location.href='login.php'; </script>";
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_usuario = $_SESSION['id_usuario'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if($nova_senha !== $confirmar_senha) {
            echo "<script> alert('As senhas não coincidem!'); </script>";
        } elseif (strlen($nova_senha) < 8) {
            echo "<script> alert('A senha deve ter pelo menos 8 caracteres!') </script>";
        } elseif ($nova_senha === "temp123") {
            echo "<script> alert('Escolha uma senha diferente da temporária!') </script>";
        } else {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            // ATUALZIZA A SENHA E REMOVE O STATUS DE TEMPORÁRIA
            $query = "UPDATE usuario SET senha = :senha, senha_temporaria = FALSE where id_usuario = :id_usuario";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":senha", $senha_hash);
            $stmt -> bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);

            if ($stmt -> execute()) {
                session_destroy(); // FINALIZA A SESSÃO

                echo "<script> alert('Senha alterada com sucesso! Faça o login novamente.'); window.location.href='index.php'; </script>";
            } else {
                echo "<script> alert('Falha ao alterar senha!'); window.location.href='index.php'; </script>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Alterar senha</h2>
    <p>Olá, <strong><?php echo $_SESSION['usuario']; ?></strong>. Digite sua nova senha abaixo:</p>

    <form action="alterar_senha.php" method="POST">
        <label for="nova_senha">Nova senha</label>
        <input type="password" name="nova_senha" id="nova_senha">

        <label for="confirmar_senha">Confirme sua senha</label>
        <input type="password" name="confirmar_senha" id="confirmar_senha">

        <label>
            <input type="checkbox" onclick="mostrarSenha()">Mostrar senha
        </label>

        <button type="submit">Salvar nova senha</button>
    </form>

    <script>
        function mostrarSenha() {
            var senha1 = document.getElementById('nova_senha');
            var senha2 = document.getElementById('confirmar_senha');
            var tipo = senha1.type === 'password' ? 'text': 'password';
            senha1.type = tipo;
            senha2.type = tipo;
        };
    </script>
</body>
</html>