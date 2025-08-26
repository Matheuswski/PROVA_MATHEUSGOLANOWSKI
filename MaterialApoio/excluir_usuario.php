<?php
    session_start();

    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSAO DE adm
    if($_SESSION['perfil'] != 1) {
        echo "<script> alert('Acesso Negado!'); window.location.href='principal.php'; </script>";
        exit();
    }

    // INCIALIZA AS VARIAVEIS
    $usuarios = null;

    // BUSCA TODOS OS USUARIOS CADASTRADOS EM ORDEM ALFABETICA
    $query = "SELECT u.*, p.nome_perfil FROM usuario as u
    INNER JOIN perfil as p WHERE u.id_perfil = p.id_perfil
    ORDER BY nome ASC";
    
    $stmt = $pdo -> prepare($query);
    $stmt -> execute();
    $usuarios = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    // SE UM id FOR PASSADO VIA GET, EXCLUI O usuario
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_usuario = $_GET['id'];

        // EXCLUI O USUARIO DO BANCO DE DADOS
        $query = "DELETE FROM usuario WHERE id_usuario = :id";

        $stmt = $pdo -> prepare($query);
        $stmt -> bindParam(":id", $id_usuario, PDO::PARAM_INT);
        
        if($stmt -> execute()) {
            echo "<script> alert('Usuário excluido com sucesso!'); window.location.href='buscar_usuario.php'; </script>";
        } else {
            echo "<script> alert('Erro ao excluir usuário!'); </script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>

    <link rel="stylesheet" href="styles.css">
</head>
<body> 
    <h2>Excluir Usuário</h2>

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
                            <a class="btn-excluir" href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>" onclick="return confirm('Você tem certea que deseja excluí-lo?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table></center>
        </div>
    <?php else: ?>
        <p>Nenhum consagrado encontrado!</p>
    <?php endif; ?>

    <a class="btn-voltar" href="principal.php">Voltar</a>
</body>
</html>