<?php
    session_start();

    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSAO DE adm
    if($_SESSION['perfil'] != 1) {
        echo "<script> alert('Acesso Negado!'); window.location.href='principal.php'; </script>";
        exit();
    }

    // INCIALIZA AS VARIAVEIS
    $funcionario = null;

    // SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO id OU PELO nome
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['busca_funcionario'])) {
            $busca = trim($_POST['busca_funcionario']);

            // VERIFICA SE A BUSCA É UM id OU UM nome
            if(is_numeric($busca)) {
                $query = "SELECT * FROM funcionario WHERE id_funcionario = :busca";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindParam(":busca", $busca, PDO::PARAM_INT);
            } else {
                $query = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindValue(":busca_nome", "%$busca%", PDO::PARAM_STR);
            }

            $stmt -> execute();
            $funcionario = $stmt -> fetch(PDO::FETCH_ASSOC);

            // SE O FUNCIONARIO NÃO FOR ENCONTRADO, EXIBE UM ALERTA
            if(!$funcionario) {
                echo "<script> alert('Funcionario não encontrado!'); </script>";
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_GET['id'])) {
            $busca = trim($_GET['id']);

            // VERIFICA SE A BUSCA É UM id OU UM nome
            if(is_numeric($busca)) {
                $query = "SELECT * FROM funcionario WHERE id_funcionario = :busca";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindParam(":busca", $busca, PDO::PARAM_INT);
            } else {
                $query = "SELECT * FROM funcionario WHERE nome_funcionario LIKE :busca_nome";

                $stmt = $pdo -> prepare($query);
                $stmt -> bindValue(":busca_nome", "%$busca%", PDO::PARAM_STR);
            }

            $stmt -> execute();
            $funcionario = $stmt -> fetch(PDO::FETCH_ASSOC);

            // SE O USUARIO NÃO FOR ENCONTRADO, EXIBE UM ALERTA
            if(!$funcionario) {
                echo "<script> alert('Funcionario não encontrado!'); </script>";
                }
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Funcionario</title>

    <link rel="stylesheet" href="styles.css">

    <!-- CERTIFIQUE-SE DE QUE O JavaScript ESTÁ SENDO CARREGADO CORRETAMENTE -->
     <script src="scripts.js"></script>
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
    <h2>Alterar Funcionario</h2>

    <form action="alterar_funcionario.php" method="POST">
        <label for="busca_funcionario">Digite o ID ou NOME do Funcionario:</label>
        <input type="text" name="busca_funcionario" id="busca_funcionario" required onkeyup="buscarSugestoes()">

        <div id="sugestoes"></div>

        <button type="submit">Buscar</button>
    </form>

    <?php if($funcionario): ?>
        <form action="processa_alteracao_funcionario.php" method="POST">
            <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars($funcionario['id_funcionario']) ?>">

            <label for="nome">Nome:</label>
            <input type="text" name="nome_funcionario" id="nome_funcionario" value="<?= htmlspecialchars($funcionario['nome_funcionario']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>

            <label for="id_funcionario">Perfil:</label>
            <select name="id_funcionario" id="id_perfil">
                <option value="1" <?= $funcionario['id_funcionario'] == 1 ? 'selected': '' ?>>Administrador</option>
                <option value="2" <?= $funcionario['id_funcionario'] == 2 ? 'selected': '' ?>>Secretária</option>
                <option value="3" <?= $funcionario['id_funcionario'] == 3 ? 'selected': '' ?>>Almoxarife</option>
                <option value="4" <?= $funcionario['id_funcionario'] == 4 ? 'selected': '' ?>>Cliente</option>
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


</body>
</html>