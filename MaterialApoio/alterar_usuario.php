<?php

session_start();
require_once 'conexao.php';

// VERFICIA SE O USUARIO DE ERMISSÃO DE ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// INICIALIZA As VARIAVEIS
$usuario = null;

// SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO NOME OU ID
if ($_SERVER["REQUEST_METHOD"] == "POST")
    if (!empty($_POST['busca_usuario'])) {
    $busca = trim($_POST['busca_usuario']);
    
    // VERIFICA SE É UM NÚMERO (ID) OU UM NOME
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);  
    }
    
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // SE O USUARIO FOR ENCONTRADO, EXIBE UM ALERTA
    if (!$usuario) {
        echo "<script>alert('Usuário não encontrado.');window.location.href='buscar_usuario.php';</script>";
    }
} 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuario</title>
    <link rel="stylesheet" href="styles.css">

    <!-- CERTIFIQUE-SE DE QUE O JAVASCRIPT ESTA SENDO CARREGADO CORRETAMENTE -->
    <script src="scripts.js"></script>
</head>
<body>
    <h2>Alterar Usuarios</h2>

    <!-- FORMULARIO PARA BUSCAR USUARIOS -->
    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Buscar por ID ou Nome do usuario:</label>
        <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()">

        <div id="sugestoes"></div>
        <button type="submit">Buscar</button>
    </form>

    <?php if($usuario): ?>
        <form action="processa_alteracao_usuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?=htmlspecialchars($usuario['id_usuario'])?>">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($usuario['nome'])?>" required>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required>
            <label for="id_perfil">Perfil:</label>
            <select id="id_perfil" name="id_perfil">
                <option value="1" <?=$usuario['id_perfil'] == 1 ? 'selected': ''?>>Administrador</option>
                <option value="2" <?=$usuario['id_perfil'] == 2 ? 'selected': ''?>>Secretaria</option>
                <option value="3" <?=$usuario['id_perfil'] == 3 ? 'selected': ''?>>Almoxarife</option>
                <option value="4" <?=$usuario['id_perfil'] == 4 ? 'selected': ''?>>Cliente</option>
            </select>
            <!-- SE O USUARIO LOGADO FOR ADMINISTRADOR, EXIBE O CAMPO PARA ALTERAR A SENHA -->
            <?php if ($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="senha" name="senha">
            <?php endif; ?>
            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>
    <a href="principal.php">Voltar</a>
</body>
</html>