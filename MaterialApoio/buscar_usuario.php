<?php
    session_start();

    require_once 'conexao.php';

    if($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
        echo "<script> alert('Acesso negado!'); window.location.href='principal.php'; </script>";
        exit();
    }

    // INICIALIZA A VARIÁVEL PARA EVITAR ERROS
    $usuarios = [];

    // SE O FORMULÁRIO FOR ENVIADO, BUSCA O USUÁRIO PELO ID OU NOME
    if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['busca'])) {
        $busca = trim($_POST['busca']);
        
        // VERIFICA SE A BUSCA É UM NÚMERO (id) OU UM nome
        if(is_numeric($busca)) {
            $query = "SELECT u.*, p.nome_perfil FROM usuario as u
            INNER JOIN perfil as p WHERE u.id_perfil = p.id_perfil
            AND u.id_usuario = :busca";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":busca", $busca, PDO::PARAM_INT);
        } else {
            $query = "SELECT u.*, p.nome_perfil FROM usuario as u
            INNER JOIN perfil as p WHERE u.id_perfil = p.id_perfil
            AND u.nome LIKE :busca_nome
            ORDER BY u.nome ASC";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindValue(":busca_nome", "$busca%", PDO::PARAM_STR);
        }
    } else {
        $query = "SELECT u.*, p.nome_perfil FROM usuario as u
        INNER JOIN perfil as p WHERE u.id_perfil = p.id_perfil
        ORDER BY nome ASC";

        $stmt = $pdo -> prepare($query);
    }

    $stmt -> execute();
    $usuarios = $stmt -> fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuário</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h2>Lista de Usuários</h2>

    <!-- FORMULÁRIO PARA BUSCAR USUÁRIOS -->
    <form action="buscar_usuario.php" method="POST">
        <label for="busca">Digite o ID ou NOME (opcional):</label>
        <input type="text" id="busca" name="busca">

        <button type="submit">Pesquisar</button>
    </form>

    <?php if(!empty($usuarios)): ?>
        <div class="tabela-container">
            <center><table class="tabela">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
                
                <?php foreach($usuarios as $usuario): ?>
                    <tr>
                        <td> <?= htmlspecialchars($usuario['id_usuario']); ?></td>
                        <td> <?= htmlspecialchars($usuario['nome']); ?></td>
                        <td> <?= htmlspecialchars($usuario['email']); ?></td>
                        <td> <?= htmlspecialchars($usuario['nome_perfil']); ?></td>
                        <td> 
                            <a class="btn-a" href="alterar_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>">Alterar</a>
                            <a class="btn-excluir" href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>" onclick="return confirm('Você tem certeza que deseja excluí-lo?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table></center>
            </div>
    <?php else: ?>
        <p class="aviso">Nenhum usuário encontrado!</p>
    <?php endif; ?>

    <a class="btn-voltar" href="principal.php">Voltar</a>
</body>
</html>