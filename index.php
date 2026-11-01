<?php
/**
 * DIGITAL PWA - Roteador Centralizado Pro (Multi-Tenant MVC)
 * Master: ws_admin (admin_email, admin_senha)
 * Lojista: ws_users (user_email, user_password, user_virtual_env)
 * Cliente: ws_clientes (telefone, senha)
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
    die("Erro Crítico: Arquivo Conexao.php não encontrado.");
}
require_once $dbFile;

$authFile = is_file(__DIR__ . '/app/Security/Auth.php')
    ? __DIR__ . '/app/Security/Auth.php'
    : __DIR__ . '/app/security/Auth.php';

if (!file_exists($authFile)) {
    die("Erro Crítico: Arquivo Auth.php não encontrado.");
}
require_once $authFile;

// Detecção dinâmica de pastas (evita divergência de maiúsculas/minúsculas no Linux)
$pastaLogista = is_dir(__DIR__ . '/views/admin_logista') ? 'admin_logista' : 'admin_lojista';

// Extração segura de parâmetros da URL e URI
$tenantSlug = $_GET['tenant'] ?? '';
$pagina     = $_GET['pagina'] ?? 'home';
$uriPath    = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

// ==========================================================
// ROTA 1: LANDING PAGE INSTITUCIONAL (Home Raiz)
// URL: /
// ==========================================================
if (empty($tenantSlug)) {
    $landingPath = __DIR__ . '/views/site/landing.php';
    if (file_exists($landingPath)) {
        include $landingPath;
    } else {
        echo "<h1 style='font-family:sans-serif;'>PWA Food Platform</h1>";
        echo "<p><a href='/master-admin'>Login Master</a> | <a href='/admin-lojista'>Login Lojista</a></p>";
    }
    exit;
}

// ==========================================================
// ROTA 2: PAINEL MASTER (Tabela ws_admin)
// URL: /master-admin
// ==========================================================
if ($tenantSlug === 'master-admin') {
    $acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

    // Processamento do Formulário de Login Master
    if ($acao === 'autenticar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            header("Location: /master-admin?erro=campos_obrigatorios");
            exit;
        }

        try {
            $db = Conexao::getConexao();
            $stmt = $db->prepare("SELECT * FROM ws_admin WHERE TRIM(LOWER(admin_email)) = TRIM(LOWER(?)) LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && Auth::verificarSenha($senha, $admin['admin_senha'])) {
                
                // Migração transparente de MD5 (32 chars) para SHA-256 no banco
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

    // Logout Master
    if ($acao === 'logout') {
        Auth::logout();
        header("Location: /master-admin");
        exit;
    }

    // Controle de Acesso
    if (!Auth::isMasterLoggedIn()) {
        include __DIR__ . '/views/admin_master/login.php';
        exit;
    }

    // Telas do Painel Logado
    if (file_exists(__DIR__ . '/views/admin_master/gerenciar_lojas.php')) {
        include __DIR__ . '/views/admin_master/gerenciar_lojas.php';
    } elseif (file_exists(__DIR__ . '/views/admin_master/dashboard.php')) {
        include __DIR__ . '/views/admin_master/dashboard.php';
    } else {
        echo "<div style='font-family:sans-serif; padding:40px;'>";
        echo "<h2>Painel Master Autenticado!</h2>";
        echo "<p>E-mail: " . htmlspecialchars($_SESSION['master_email'] ?? '') . "</p>";
        echo "<a href='/master-admin?acao=logout' style='color:red;'>Sair</a>";
        echo "</div>";
    }
    exit;
}

// ==========================================================
// ROTA 3: LOGIN CENTRALIZADO DO LOJISTA (Tabela ws_users)
// URL: /admin-lojista
// ==========================================================
if ($tenantSlug === 'admin-lojista') {
    $acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

    // Processamento do Formulário de Login do Lojista
    if ($acao === 'autenticar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            header("Location: /admin-lojista?erro=campos_obrigatorios");
            exit;
        }

        try {
            $db = Conexao::getConexao();
            $stmt = $db->prepare("SELECT * FROM ws_users WHERE TRIM(LOWER(user_email)) = TRIM(LOWER(?)) LIMIT 1");
            $stmt->execute([$email]);
            $lojista = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lojista && Auth::verificarSenha($senha, $lojista['user_password'])) {

                // Bloqueia se inativo
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

                // Redireciona diretamente para o painel exclusivo daquela loja
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

    // Se o slug não existir em ws_users
    if (!$loja) {
        header("Location: /?erro=loja_nao_encontrada");
        exit;
    }

    $_SESSION['current_tenant_id'] = (int)$loja['user_id'];

    // ------------------------------------------------------
    // 4.1: API DO PEDIDO - GRAVAÇÃO DIRETA NA BASE DE DADOS
    // URL: /{user_virtual_env}?acao_cliente=salvar_pedido
    // ------------------------------------------------------
    if (isset($_GET['acao_cliente']) && $_GET['acao_cliente'] === 'salvar_pedido') {
        require_once __DIR__ . '/app/controllers/ClienteController.php';
        $cliCtrl = new ClienteController();
        $cliCtrl->salvarPedido($loja);
        exit;
    }

    // ------------------------------------------------------
    // 4.2: POLLING DO ALARME SONORO DO DASHBOARD DO LOJISTA
    // URL: /{user_virtual_env}?acao_loja=checar_novos_pedidos
    // ------------------------------------------------------
    if (isset($_GET['acao_loja']) && $_GET['acao_loja'] === 'checar_novos_pedidos') {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $stmtNovos = $db->prepare("SELECT id, codigo_pedido, nome, total FROM ws_pedidos WHERE user_id = ? AND view = 0 ORDER BY id DESC");
        $stmtNovos->execute([(int)$loja['user_id']]);
        $novos = $stmtNovos->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($novos)) {
            $up = $db->prepare("UPDATE ws_pedidos SET view = 1 WHERE user_id = ? AND view = 0");
            $up->execute([(int)$loja['user_id']]);
        }

        echo json_encode(['tem_novos' => !empty($novos), 'pedidos' => $novos], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ------------------------------------------------------
    // 4.3: SUB-ROTAS DE LOGIN, CADASTRO E LOGOUT DO CONSUMIDOR
    // URL: /{user_virtual_env}?acao_cliente=login | registrar | logout
    // ------------------------------------------------------
    if (isset($_GET['acao_cliente']) || $pagina === 'cliente') {
        require_once __DIR__ . '/app/controllers/ClienteController.php';
        $cliCtrl = new ClienteController();
        $acaoCli = $_GET['acao_cliente'] ?? ($_GET['sub'] ?? 'login');

        if ($acaoCli === 'registrar') {
            $cliCtrl->registrar($loja);
        } elseif ($acaoCli === 'logout') {
            $cliCtrl->logout($loja);
        } else {
            $cliCtrl->login($loja);
        }
        exit;
    }

    // ------------------------------------------------------
    // 4.4: TELA DE COZINHA / KDS (PEDIDOS EM PREPARO)
    // URL: /{user_virtual_env}/cozinha
    // ------------------------------------------------------
    if ($pagina === 'cozinha' || str_contains($uriPath, "/cozinha")) {
        if (file_exists(__DIR__ . '/views/cozinha/pedidos.php')) {
            include __DIR__ . '/views/cozinha/pedidos.php';
        } elseif (file_exists(__DIR__ . '/views/cozinha/index.php')) {
            include __DIR__ . '/views/cozinha/index.php';
        } else {
            echo "<h2 style='font-family:sans-serif; padding:30px;'>Tela de Cozinha (KDS) em desenvolvimento.</h2>";
        }
        exit;
    }

    // ------------------------------------------------------
    // 4.5: PAINEL ADMINISTRATIVO DO LOJISTA
    // URL: /{user_virtual_env}/admin
    // ------------------------------------------------------
    $isAdminRoute = ($pagina === 'admin') || str_contains($uriPath, "/admin");

    if ($isAdminRoute) {
        $acao = $_POST['acao'] ?? ($_GET['acao'] ?? '');

        // Logout exclusivo do lojista
        if ($acao === 'logout') {
            Auth::logout();
            header("Location: /admin-lojista");
            exit;
        }

        // Submissão do login direto do tenant (caso tente logar via /{slug}/admin)
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

        // Bloqueia e exibe a tela de login se não estiver logado para este tenant
        if (!Auth::isLojistaLoggedIn((int)$loja['user_id'])) {
            include __DIR__ . "/views/{$pastaLogista}/login.php";
            exit;
        }

        // Carrega o Controller do Lojista
        require_once __DIR__ . '/app/controllers/LojistaController.php';
        $lojistaCtrl = new LojistaController();

        // Extração robusta da sub-seção:
        // Suporta tanto Query String (?secao=produtos) quanto URLs amigáveis (/admin/produtos)
        $subSecao = $_GET['secao'] ?? ($_GET['p'] ?? '');

        if (empty($subSecao)) {
            $partesUri = explode('/', trim($uriPath, '/'));
            $posAdmin = array_search('admin', $partesUri);
            if ($posAdmin !== false && isset($partesUri[$posAdmin + 1])) {
                $subSecao = $partesUri[$posAdmin + 1];
            }
        }

        if (empty($subSecao) || $subSecao === 'admin') {
            $subSecao = 'dashboard';
        }

        // Mapeamento completo de todas as seções administrativas
        switch ($subSecao) {
            case 'produtos':
                $lojistaCtrl->produtos($loja);
                break;
            case 'categorias':
                $lojistaCtrl->categorias($loja);
                break;
            case 'taxas':
                $lojistaCtrl->taxas($loja);
                break;
            case 'horarios':
                $lojistaCtrl->horarios($loja);
                break;
            case 'configuracoes':
                $lojistaCtrl->configuracoes($loja);
                break;
            case 'clientes':
                $lojistaCtrl->clientes($loja);
                break;
            case 'dashboard':
            default:
                $lojistaCtrl->dashboard($loja);
                break;
        }
        exit;
    }

    // ------------------------------------------------------
    // 4.6: CARDÁPIO DIGITAL DO CLIENTE FINAL (PWA)
    // URL: /{user_virtual_env} (Ex: /hugolanches)
    // ------------------------------------------------------
    $ctrlFile = is_file(__DIR__ . '/app/controllers/HomeController.php')
        ? __DIR__ . '/app/controllers/HomeController.php'
        : __DIR__ . '/app/Controllers/HomeController.php';

    if (!file_exists($ctrlFile)) {
        die("Erro Crítico: HomeController.php não foi localizado em 'app/controllers/'.");
    }

    require_once $ctrlFile;
    $controller = new HomeController();
    $controller->cardapio($loja);
    exit;

} catch (Exception $e) {
    $erroCriticoPath = __DIR__ . '/views/site/erro_critico.php';
    if (file_exists($erroCriticoPath)) {
        include $erroCriticoPath;
    } else {
        die("<div style='font-family:sans-serif; padding:40px; background:#fff3cd; color:#856404;'>
                <h3>Erro no Servidor</h3>
                <p>" . htmlspecialchars($e->getMessage()) . "</p>
                <p><a href='javascript:history.back()'>Voltar</a></p>
             </div>");
    }
    exit;
}