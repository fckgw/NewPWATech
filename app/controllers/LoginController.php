<?php
session_start();
require_once '../Database/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $db = Conexao::getConexao();
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nivel'] = $user['nivel'];
        $_SESSION['tenant_id'] = $user['tenant_id'];

        if ($user['nivel'] === 'admin_master') {
            header("Location: /master-admin");
        } else {
            // Pega o slug da lanchonete para redirecionar corretamente
            $st = $db->prepare("SELECT slug FROM tenants WHERE id = ?");
            $st->execute([$user['tenant_id']]);
            $t = $st->fetch();
            header("Location: /" . $t['slug'] . "/admin");
        }
    } else {
        header("Location: /login?erro=invalido");
    }
}