<?php
session_start();

require_once 'conexao.php';

if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script> alert('Acesso negado!'); window.location.href='principal.php'; </script>";
    exit();
}

// INICIALIZA A VARIÁVEL PARA EVITAR ERROS
$funcionarios = [];

// SE O FORMULÁRIO FOR ENVIADO, BUSCA O USUÁRIO PELO ID OU NOME
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    // VERIFICA SE A BUSCA É UM NÚMERO (id) OU UM nome

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
    <title>Buscar Funcionario</title>
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

    <h2>Lista de Funcionarios</h2>

    <!-- FORMULÁRIO PARA BUSCAR USUÁRIOS -->
    <form action="buscar_funcionario.php" method="POST">
        <label for="busca">Digite o ID ou NOME (opcional):</label>
        <input type="text" id="busca" name="busca">

        <button type="submit">Pesquisar</button>
    </form>

    <?php if (!empty($funcionarios)): ?>
        <div class="tabela-container">
            <center>
                <table class="tabela">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Telefone</th>
                        <th>E-Mail</th>
                    </tr>

                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr>
                            <td> <?= htmlspecialchars($funcionario['id_funcionario']); ?></td>
                            <td> <?= htmlspecialchars($funcionario['nome_funcionario']); ?></td>
                            <td> <?= htmlspecialchars($funcionario['endereco']); ?></td>
                            <td> <?= htmlspecialchars($funcionario['telefone']); ?></td>
                            <td> <?= htmlspecialchars($funcionario['email']); ?></td>
                            <td>
                                <a class="btn-a"
                                    href="alterar_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>">Alterar</a>
                                <a class="btn-excluir"
                                    href="excluir_funcionario.php?id=<?= htmlspecialchars($funcionario['id_funcionario']) ?>"
                                    onclick="return confirm('Você tem certeza que deseja excluí-lo?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </center>
        </div>
    <?php else: ?>
        <p class="aviso">Nenhum funcionario encontrado!</p>
    <?php endif; ?>


</body>

</html>