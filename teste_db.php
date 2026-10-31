<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/Database/Conexao.php';
require_once __DIR__ . '/app/Security/Auth.php';

echo "<h2 style='font-family:sans-serif;'>Diagnóstico de Senhas e Hashes</h2>";

try {
    $db = Conexao::getConexao();

    // ========================================================
    // 1. TESTE DO MASTER
    // ========================================================
    echo "<h3 style='font-family:sans-serif; color:#4338ca;'>1. Verificação do Master (ws_admin):</h3>";
    $emailMaster = 'souza.ffr@icloud.com';
    $senhaDigitadaMaster = 'Fckgw!151289';

    $stmtM = $db->prepare("SELECT admin_id, admin_email, admin_senha FROM ws_admin WHERE TRIM(LOWER(admin_email)) = TRIM(LOWER(?)) LIMIT 1");
    $stmtM->execute([$emailMaster]);
    $admin = $stmtM->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $hashBancoMaster = trim($admin['admin_senha']);
        $hashCalculadoMaster = hash('sha256', $senhaDigitadaMaster);
        $md5CalculadoMaster = md5($senhaDigitadaMaster);

        echo "<pre style='background:#f4f4f4; padding:15px; border-radius:8px;'>";
        echo "E-mail encontrado no banco: " . $admin['admin_email'] . "\n";
        echo "Senha digitada:             " . $senhaDigitadaMaster . "\n";
        echo "Hash salvo no banco:        " . $hashBancoMaster . " (tamanho: " . strlen($hashBancoMaster) . " caracteres)\n";
        echo "Hash SHA-256 gerado agora:  " . $hashCalculadoMaster . "\n";
        echo "Hash MD5 gerado agora:      " . $md5CalculadoMaster . "\n";
        
        $confereMaster = Auth::verificarSenha($senhaDigitadaMaster, $hashBancoMaster);
        echo "Resultado Auth::verificarSenha: " . ($confereMaster ? "<b style='color:green;'>OK (AUTENTICARIA COM SUCESSO!)</b>" : "<b style='color:red;'>FALHOU</b>") . "\n";
        echo "</pre>";

        if (!$confereMaster) {
            echo "<p><b>Corrigindo automaticamente o Master para SHA-256...</b></p>";
            $upM = $db->prepare("UPDATE ws_admin SET admin_senha = ? WHERE admin_id = ?");
            $upM->execute([$hashCalculadoMaster, $admin['admin_id']]);
            echo "<p style='color:green;'><b>Senha do Master atualizada com sucesso para SHA-256!</b></p>";
        }
    } else {
        echo "<p style='color:red;'>Usuário master com e-mail '{$emailMaster}' não foi encontrado na tabela ws_admin!</p>";
    }

    // ========================================================
    // 2. TESTE DO LOJISTA
    // ========================================================
    echo "<h3 style='font-family:sans-serif; color:#15803d;'>2. Verificação do Lojista (ws_users):</h3>";
    $emailLojista = 'souza.ffr@gmail.com';
    $senhaDigitadaLojista = 'BDSoft@2020';

    $stmtL = $db->prepare("SELECT user_id, user_email, user_virtual_env, user_password, user_status FROM ws_users WHERE TRIM(LOWER(user_email)) = TRIM(LOWER(?)) LIMIT 1");
    $stmtL->execute([$emailLojista]);
    $lojista = $stmtL->fetch(PDO::FETCH_ASSOC);

    if ($lojista) {
        $hashBancoLojista = trim($lojista['user_password']);
        $hashCalculadoLojista = hash('sha256', $senhaDigitadaLojista);
        $md5CalculadoLojista = md5($senhaDigitadaLojista);

        echo "<pre style='background:#f4f4f4; padding:15px; border-radius:8px;'>";
        echo "E-mail encontrado no banco: " . $lojista['user_email'] . "\n";
        echo "Slug da loja:               " . $lojista['user_virtual_env'] . "\n";
        echo "Status da loja:             " . $lojista['user_status'] . "\n";
        echo "Senha digitada:             " . $senhaDigitadaLojista . "\n";
        echo "Hash salvo no banco:        " . $hashBancoLojista . " (tamanho: " . strlen($hashBancoLojista) . " caracteres)\n";
        echo "Hash SHA-256 gerado agora:  " . $hashCalculadoLojista . "\n";
        echo "Hash MD5 gerado agora:      " . $md5CalculadoLojista . "\n";

        $confereLojista = Auth::verificarSenha($senhaDigitadaLojista, $hashBancoLojista);
        echo "Resultado Auth::verificarSenha: " . ($confereLojista ? "<b style='color:green;'>OK (AUTENTICARIA COM SUCESSO!)</b>" : "<b style='color:red;'>FALHOU</b>") . "\n";
        echo "</pre>";

        if (!$confereLojista) {
            echo "<p><b>Corrigindo automaticamente o Lojista para SHA-256...</b></p>";
            $upL = $db->prepare("UPDATE ws_users SET user_password = ? WHERE user_id = ?");
            $upL->execute([$hashCalculadoLojista, $lojista['user_id']]);
            echo "<p style='color:green;'><b>Senha do Lojista atualizada com sucesso para SHA-256!</b></p>";
        }
    } else {
        echo "<p style='color:red;'>Lojista com e-mail '{$emailLojista}' não foi encontrado na tabela ws_users!</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
}