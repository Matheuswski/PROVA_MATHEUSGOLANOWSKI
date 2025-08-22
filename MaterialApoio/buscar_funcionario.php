<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['perfil']) || ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2)) {
    echo "<script>alert('Acesso negado. Você não tem permissão para acessar esta página.'); window.location.href='principal.php';</script>";
    exit();
}


// Inicializa a variavel para evitar erros
$funcionarios = [];

// Se o formulario for enviado, busca o usuario pelo id ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    // Verifica se a busca é um número (ID) ou um nome
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM funcionario WHERE id_funcionario = :busca ORDER BY nome_funcionario ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":busca", $busca, PDO::PARAM_INT); // Busca por ID
    } else {
        $sql = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome ORDER BY nome_funcionario ASC";
        $stmt->bindValue(":busca_nome", "%$busca%", PDO::PARAM_STR);
        $busca = "%$busca%"; // Para busca por nome
    }
} else {
    // Busca todos os usuarios se o formulario não for enviado
    $sql = "SELECT * FROM funcionario ORDER BY nome_funcionario ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Funcionario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Lista de Usuarios</h2>
    <!-- Formulário de busca -->
    <form action="buscar_funcionario.php" method="post">
        <label for="busca">Digite o ID ou Nome(opcional):</label>
        <input type="text" id="busca" name="busca" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if(!empty($funcionarios)): ?>
        <center>
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                    
                </tr>
                <?php foreach($funcionarios as $funcionario): ?>
                    <tr>
                        <td><?= htmlspecialchars($funcionario['id_funcionario']) ?></td>
                        <td><?= htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                        <td><?= htmlspecialchars($funcionario['endereco']) ?></td>
                        <td><?= htmlspecialchars($funcionario['telefone']) ?></td>
                        <td><?= htmlspecialchars($funcionario['email']) ?></td>
                        <td>
                            <a href="alterar_usuario.php?id=<?=htmlspecialchars($funcionario['id_funcionario']) ?>">Alterar</a> 
                            <a href="excluir_usuario.php?id=<?=htmlspecialchars($funcionario['id_funcionario']) ?>" onclick="return confirm('Tem certeza que deseja excluir este funcionario?')">Excluir</a> 
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </center>
    <?php else: ?>
        <p>Nenhum funcionario encontrado.</p>
    <?php endif; ?>
    <br>
    <a href="principal.php">Voltar</a>
</body>
</html>