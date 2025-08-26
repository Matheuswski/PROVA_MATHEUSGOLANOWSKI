<?php
    session_start();

    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSAO DE adm
    if($_SESSION['perfil'] != 1) {
        echo "<script> alert('Acesso Negado!'); window.location.href='principal.php'; </script>";
        exit();
    }

    // INCIALIZA AS VARIAVEIS
    $usuario = null;

    // SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO id OU PELO nome
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['busca_usuario'])) {
            $busca = trim($_POST['busca_usuario']);

            // VERIFICA SE A BUSCA É UM id OU UM nome
            if(is_numeric($busca)) {
                $query = "SELECT * FROM usuario WHERE id_usuario = :busca";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindParam(":busca", $busca, PDO::PARAM_INT);
            } else {
                $query = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindValue(":busca_nome", "%$busca%", PDO::PARAM_STR);
            }

            $stmt -> execute();
            $usuario = $stmt -> fetch(PDO::FETCH_ASSOC);

            // SE O USUARIO NÃO FOR ENCONTRADO, EXIBE UM ALERTA
            if(!$usuario) {
                echo "<script> alert('Usuário não encontrado!'); </script>";
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_GET['id'])) {
            $busca = trim($_GET['id']);

            // VERIFICA SE A BUSCA É UM id OU UM nome
            if(is_numeric($busca)) {
                $query = "SELECT * FROM usuario WHERE id_usuario = :busca";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindParam(":busca", $busca, PDO::PARAM_INT);
            } else {
                $query = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindValue(":busca_nome", "%$busca%", PDO::PARAM_STR);
            }

            $stmt -> execute();
            $usuario = $stmt -> fetch(PDO::FETCH_ASSOC);

            // SE O USUARIO NÃO FOR ENCONTRADO, EXIBE UM ALERTA
            if(!$usuario) {
                echo "<script> alert('Usuário não encontrado!'); </script>";
                }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>

    <link rel="stylesheet" href="styles.css">

    <!-- CERTIFIQUE-SE DE QUE O JavaScript ESTÁ SENDO CARREGADO CORRETAMENTE -->
     <script src="scripts.js"></script>
</head>
<body>
    <h2>Alterar Usuário</h2>

    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Digite o ID ou NOME do Usuário:</label>
        <input type="text" name="busca_usuario" id="busca_usuario" required onkeyup="buscarSugestoes()">

        <div id="sugestoes"></div>

        <button type="submit">Buscar</button>
    </form>

    <?php if($usuario): ?>
        <form action="processa_alteracao_usuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">

            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

            <label for="id_perfil">Perfil:</label>
            <select name="id_perfil" id="id_perfil">
                <option value="1" <?= $usuario['id_perfil'] == 1 ? 'selected': '' ?>>Administrador</option>
                <option value="2" <?= $usuario['id_perfil'] == 2 ? 'selected': '' ?>>Secretária</option>
                <option value="3" <?= $usuario['id_perfil'] == 3 ? 'selected': '' ?>>Almoxarife</option>
                <option value="4" <?= $usuario['id_perfil'] == 4 ? 'selected': '' ?>>Cliente</option>
            </select>

            <!-- SE O USUÁRIO LOGADO FOR adm, EXIBIR OPÇÃO DE ALTERAR SENHA -->
            <?php if($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" name="nova_senha" id="nova_senha">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <a class="btn-voltar" href="principal.php">Voltar</a>
</body>
</html>