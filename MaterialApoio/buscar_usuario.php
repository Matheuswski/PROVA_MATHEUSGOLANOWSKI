<?php
session_start();
require_once 'conexao.php';

if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso negado. Você não tem permissão para acessar esta página.'); window.location.href='principal.php';</script>";
    exit();
}

// Inicializa a variavel para evitar erros
$usuarios = [];

// Se o formulario for enviado, busca o usuario pelo id ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);

    // Verifica se a busca é um número (ID) ou um nome
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":busca", $busca, PDO::PARAM_INT); // Busca por ID
    } else {
        $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":busca_nome", "%$busca%", PDO::PARAM_STR); // Busca por nome
        $busca = "%$busca%"; // Para busca por nome
    }
} else {
    // Busca todos os usuarios se o formulario não for enviado
    $sql = "SELECT * FROM usuario ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Lista de Usuarios</h2>
    <!-- Formulário de busca -->
    <form action="buscar_usuario.php" method="post">
        <label for="busca">Digite o ID ou Nome(opcional):</label>
        <input type="text" id="busca" name="busca" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if(!empty($usuarios)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
            <?php foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['id_perfil']) ?></td>
                    <td>
                        <a href="alterar_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario']) ?>">Alterar</a> 
                        <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario']) ?>" onclick="return confirm('Tem certeza que deseja excluir este usuario?')">Excluir</a> 
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum usuário encontrado.</p>
    <?php endif; ?>
    <a href="principal.php">Voltar</a>
</body>
</html>