<?php
session_start();

require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSAO DE adm
if ($_SESSION['perfil'] != 1) {
    echo "<script> alert('Acesso Negado!'); window.location.href='principal.php'; </script>";
    exit();
}

// INCIALIZA AS VARIAVEIS
$funcionarios = null;

// BUSCA TODOS OS FUNCIONARIOS CADASTRADOS EM ORDEM ALFABETICA
$query = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SE UM id FOR PASSADO VIA GET, EXCLUI O usuario
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_funcionario = $_GET['id'];

    // EXCLUI O FUNCIONARIO DO BANCO DE DADOS
    $query = "DELETE FROM funcionario WHERE id_funcionario = :id_funcionario";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id_funcionario", $id_funcionario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script> alert('Funcionário excluido com sucesso!'); window.location.href='excluir_funcionario.php'; </script>";
    } else {
        echo "<script> alert('Erro ao excluir funcionário!'); </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Funcionario</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h2>Excluir Funcionário</h2>
    <div class="lista-usuarios">

        <?php if (!empty($funcionarios)): ?>
            <table border>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>

                <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        <td> <?= htmlspecialchars($funcionario['id_funcionario']); ?></td>
                        <td> <?= htmlspecialchars($funcionario['nome_funcionario']); ?></td>
                        <td> <?= htmlspecialchars($funcionario['endereco']); ?></td>
                        <td> <?= htmlspecialchars($funcionario['telefone']); ?></td>
                        <td> <?= htmlspecialchars($funcionario['email']); ?></td>
                        <td>
                            <a href="excluir_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>"
                                onclick="return confirm('Você tem certeza que deseja excluí-lo?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Nenhum consagrado encontrado!</p>
        <?php endif; ?>
    </div>
    <a href="principal.php" class="btn">Voltar</a>

</body>

</html>