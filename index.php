<?php
/**
 * DIGITAL PWA - Roteador Centralizado Pro
 * Master: ws_admin (admin_email, admin_senha)
 * Lojista: ws_users (user_email, user_password, user_virtual_env)
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================================
// 1. CARREGAMENTO DO BANCO DE DADOS & SEGURANÇA
// ==========================================================
$dbFile = is_file(__DIR__ . '/app/Database/Conexao.php')
    ? __DIR__ . '/app/Database/Conexao.php'
    : __DIR__ . '/app/database/Conexao.php';

if (!file_exists($dbFile)) {
    die("Erro: Arquivo Conexao.php não encontrado.");
}
require_once $dbFile;

$authFile = is_file(__DIR__ . '/app/Security/Auth.php')
    ? __DIR__ . '/app/Security/Auth.php'
    : __DIR__ . '/app/security/Auth.php';

if (!file_exists($authFile)) {
    die("Erro: Arquivo Auth.php não encontrado.");
}
require_once $authFile;

$pastaLogista = is_dir(__DIR__ . '/views/admin_logista') ? 'admin_logista' : 'admin_lojista';

$tenantSlug = $_GET['tenant'] ?? '';
$pagina     = $_GET['pagina'] ?? 'home';

// ==========================================================
// ROTA 1: LANDING PAGE (Home Principal)
// URL: /
// ==========================================================
if (empty($tenantSlug)) {
    include __DIR__ . '/views/site/landing.php';
    exit;
}

// ==========================================================
// ROTA 2: PAINEL MASTER (Tabela ws_admin)
// URL: /master-admin
// ==========================================================
if ($tenantSlug === 'master-admin') {
    $acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

    if ($acao === 'autenticar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            header("Location: /master-admin?erro=campos_obrigatorios");
            exit;
        }

        try {
            $db = Conexao::getConexao();

            // Busca pelo admin_email na tabela ws_admin
            $stmt = $db->prepare("SELECT * FROM ws_admin WHERE TRIM(LOWER(admin_email)) = TRIM(LOWER(?)) LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && Auth::verificarSenha($senha, $admin['admin_senha'])) {
                
                // Se a senha estiver em MD5 antigo (32 chars), migra para SHA-256 automaticamente
                if (strlen(trim($admin['admin_senha'])) === 32) {
                    $up = $db->prepare("UPDATE ws_admin SET admin_senha = ? WHERE admin_id = ?");
                    $up->execute([Auth::hashSenha($senha), $admin['admin_id']]);
                }

                Auth::loginMaster($admin);
                session_write_close();
                header("Location: /master-admin");
                exit;
            } else {
                header("Location: /master-admin?erro=credenciais_invalidas");
                exit;
            }
        } catch (Exception $e) {
            die("Erro ao consultar ws_admin: " . htmlspecialchars($e->getMessage()));
        }
    }

    if ($acao === 'logout') {
        Auth::logout();
        header("Location: /master-admin");
        exit;
    }

    if (!Auth::isMasterLoggedIn()) {
        include __DIR__ . '/views/admin_master/login.php';
        exit;
    }

    if (file_exists(__DIR__ . '/views/admin_master/gerenciar_lojas.php')) {
        include __DIR__ . '/views/admin_master/gerenciar_lojas.php';
    } elseif (file_exists(__DIR__ . '/views/admin_master/dashboard.php')) {
        include __DIR__ . '/views/admin_master/dashboard.php';
    } else {
        echo "<h2>Painel Master Autenticado!</h2>";
        echo "<p>E-mail: " . htmlspecialchars($_SESSION['master_email'] ?? '') . "</p>";
        echo "<a href='/master-admin?acao=logout'>Sair</a>";
    }
    exit;
}

// ==========================================================
// ROTA 3: LOGIN DO LOJISTA (Tabela ws_users)
// URL: /admin-lojista
// ==========================================================
if ($tenantSlug === 'admin-lojista') {
    $acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

    if ($acao === 'autenticar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            header("Location: /admin-lojista?erro=campos_obrigatorios");
            exit;
        }

        try {
            $db = Conexao::getConexao();

            // Busca na ws_users por user_email
            $stmt = $db->prepare("SELECT * FROM ws_users WHERE TRIM(LOWER(user_email)) = TRIM(LOWER(?)) LIMIT 1");
            $stmt->execute([$email]);
            $lojista = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lojista && Auth::verificarSenha($senha, $lojista['user_password'])) {

                // Validação de status caso exista preenchido
                if (!empty($lojista['user_status']) && strtolower(trim($lojista['user_status'])) !== 'ativo') {
                    header("Location: /admin-lojista?erro=inativo");
                    exit;
                }

                // Migração transparente de MD5 para SHA-256
                if (strlen(trim($lojista['user_password'])) === 32) {
                    $up = $db->prepare("UPDATE ws_users SET user_password = ? WHERE user_id = ?");
                    $up->execute([Auth::hashSenha($senha), $lojista['user_id']]);
                }

                Auth::loginLojista($lojista);
                session_write_close();

                // Redireciona diretamente para o slug da loja (Ex: /hugolanches/admin)
                $slug = trim($lojista['user_virtual_env']);
                header("Location: /" . $slug . "/admin");
                exit;

            } else {
                header("Location: /admin-lojista?erro=credenciais_invalidas");
                exit;
            }

        } catch (Exception $e) {
            die("Erro ao consultar ws_users: " . htmlspecialchars($e->getMessage()));
        }
    }

    include __DIR__ . "/views/{$pastaLogista}/login.php";
    exit;
}

// ==========================================================
// ROTA 4: AMBIENTE MULTI-TENANT (Loja Específica via ws_users)
// URL: /{user_virtual_env} OU /{user_virtual_env}/admin
// ==========================================================
try {
    $db = Conexao::getConexao();
    $stmt = $db->prepare("SELECT * FROM ws_users WHERE TRIM(LOWER(user_virtual_env)) = TRIM(LOWER(?)) LIMIT 1");
    $stmt->execute([$tenantSlug]);
    $loja = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$loja) {
        header("Location: /?erro=loja_nao_encontrada");
        exit;
    }

    $_SESSION['current_tenant_id'] = (int)$loja['user_id'];

    // ------------------------------------------------------
    // 4.1: PAINEL DA LOJA
    // URL: /{user_virtual_env}/admin
    // ------------------------------------------------------
    if ($pagina === 'admin') {
        $acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

        if ($acao === 'logout') {
            Auth::logout();
            header("Location: /admin-lojista");
            exit;
        }

        if ($acao === 'autenticar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            if (strtolower($email) === strtolower(trim($loja['user_email'])) && Auth::verificarSenha($senha, $loja['user_password'])) {
                Auth::loginLojista($loja);
                session_write_close();
                header("Location: /{$tenantSlug}/admin");
                exit;
            } else {
                header("Location: /{$tenantSlug}/admin?erro=credenciais_invalidas");
                exit;
            }
        }

        if (!Auth::isLojistaLoggedIn((int)$loja['user_id'])) {
            include __DIR__ . "/views/{$pastaLogista}/login.php";
            exit;
        }

        $dashPath = __DIR__ . "/views/{$pastaLogista}/dashboard.php";
        if (file_exists($dashPath)) {
            include $dashPath;
        } else {
            echo "<h2>Painel da Loja: " . htmlspecialchars($loja['user_name']) . "</h2>";
            echo "<p><a href='/" . htmlspecialchars($loja['user_virtual_env']) . "'>Ver Cardápio Público</a></p>";
            echo "<p><a href='/" . htmlspecialchars($loja['user_virtual_env']) . "/admin?acao=logout'>Sair</a></p>";
        }
        exit;
    }

    // ------------------------------------------------------
    // 4.2: CARDÁPIO PWA DO CLIENTE FINAL
    // URL: /{user_virtual_env} (Ex: /hugolanches)
    // ------------------------------------------------------
    $ctrlFile = is_file(__DIR__ . '/app/controllers/HomeController.php')
        ? __DIR__ . '/app/controllers/HomeController.php'
        : __DIR__ . '/app/Controllers/HomeController.php';

    require_once $ctrlFile;
    $controller = new HomeController();
    $controller->cardapio($loja);
    exit;

} catch (Exception $e) {
    include __DIR__ . '/views/site/erro_critico.php';
}