<?php
    session_start();
    
    require_once 'conexao.php';

    if($_SESSION['perfil'] != 1) {
        echo "<script> alert('Acesso Negado!'); window.location.href='principal.php'; </script>";
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_funcionario = $_POST['id_fornecedor'];
        $nome_funcionario = $_POST['nome_funcionario'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];

        // ATUALIZA OS DADOS DO USUÁRIO
        if($nova_senha) {
            $query = "UPDATE funcionario SET nome_funcionario = :nome_funcionario, endereco = :endereco, telefone = :telefone, email = :email, WHERE id_funcionario = :id_funcionario";

            $stmt = $pdo -> prepare($query);
            $stmt -> bindParam(":senha", $nova_senha);
        } else {
            $query = "UPDATE usuario SET nome-funcionario = :nome-funcionario, email = :email WHERE id_funcionario = :id_funcionario";

            $stmt = $pdo -> prepare($query);
        }

        $stmt -> bindParam(":nome_funcionario", $nome_);
        $stmt -> bindParam(":email", $email);
        $stmt -> bindParam(":id_funcionario", $id_funcionario);

        if($stmt -> execute()) {
            echo "<script> alert('funcionario alterado com sucesso!'); window.location.href='buscar_usuario.php'; </script>";
        } else {
            echo "<script> alert('Erro ao atualizar funcionario!'); window.location.href='alterar_usuario.php'; </script>";
        }
    }
?>