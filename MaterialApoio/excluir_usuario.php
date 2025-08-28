<?php
session_start();

require_once 'conexao.php';

// VERIFICA SE O USUARIO TEM PERMISSAO DE adm
if ($_SESSION['perfil'] != 1) {
    echo "<script> alert('Acesso Negado!'); window.location.href='principal.php'; </script>";
    exit();
}

// INCIALIZA AS VARIAVEIS
$usuarios = null;

// BUSCA TODOS OS USUARIOS CADASTRADOS EM ORDEM ALFABETICA
$query = "SELECT u.*, p.nome_perfil FROM usuario as u
    INNER JOIN perfil as p WHERE u.id_perfil = p.id_perfil
    ORDER BY nome ASC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SE UM id FOR PASSADO VIA GET, EXCLUI O usuario
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // EXCLUI O USUARIO DO BANCO DE DADOS
    $query = "DELETE FROM usuario WHERE id_usuario = :id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id_usuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script> alert('Usuário excluido com sucesso!'); window.location.href='buscar_usuario.php'; </script>";
    } else {
        echo "<script> alert('Erro ao excluir usuário!'); </script>";
    }
}
// Definição das permissões por perfil
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// Definição das permissões por perfil
$permissoes = [
    1=>["Cadastrar"=>["cadastro_usuario.php","cadastro_perfil.php","cadastro_cliente.php","cadastro_fornecedor.php","cadastro_produto.php","cadastrar_funcionario.php"],
        "Buscar"=>["buscar_usuario.php","buscar_perfil.php","buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php","buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php","alterar_perfil.php","alterar_cliente.php","alterar_fornecedor.php","alterar_produto.php","alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php","excluir_perfil.php","excluir_cliente.php","excluir_fornecedor.php","excluir_produto.php","excluir_funcionario.php"]],

    2=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php","alterar_fornecedor.php"]],

    3=>["Cadastrar"=>["cadastro_fornecedor.php","cadastro_produto.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"=>["alterar_fornecedor.php","alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],

    4=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php"]],
];

// Obtendo as opções disponíveis para o perfil do usuário logado
$opcoes_menu = $permissoes[$id_perfil];
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
<nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <h2>Excluir Usuário</h2>

    <?php if (!empty($usuarios)): ?>
        <div class="tabela-container">
            <center>
                <table class="tabela">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>

                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td> <?= htmlspecialchars($usuario['id_usuario']); ?></td>
                            <td> <?= htmlspecialchars($usuario['nome']); ?></td>
                            <td> <?= htmlspecialchars($usuario['email']); ?></td>
                            <td> <?= htmlspecialchars($usuario['nome_perfil']); ?></td>
                            <td>
                                <a class="btn-excluir"
                                    href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>"
                                    onclick="return confirm('Você tem certea que deseja excluí-lo?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </center>
        </div>
    <?php else: ?>
        <p>Nenhum consagrado encontrado!</p>
    <?php endif; ?>


</body>

</html>