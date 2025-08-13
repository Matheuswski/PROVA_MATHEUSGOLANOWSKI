<?php
session_start();
require_once 'conexao.php';

//GARANTE QUE O USUARIO ESTEJA LOGADO
if (!isset($_SESSION['usuario'])) {
    echo"<script>alert('Acesso Negado'); window.location.href='login.php';</script>";
    exit();
}