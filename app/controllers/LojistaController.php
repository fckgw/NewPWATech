<?php

// Conexão e Segurança
$dbPath = is_file(__DIR__ . '/../Database/Conexao.php') ? __DIR__ . '/../Database/Conexao.php' : __DIR__ . '/../database/Conexao.php';
require_once $dbPath;

$authPath = is_file(__DIR__ . '/../Security/Auth.php') ? __DIR__ . '/../Security/Auth.php' : __DIR__ . '/../security/Auth.php';
require_once $authPath;

// Carregamento defensivo de WhatsAppService (suporta app/Services ou app/services)
$whatsPaths = [
    __DIR__ . '/../Services/WhatsAppService.php',
    __DIR__ . '/../services/WhatsAppService.php',
    __DIR__ . '/../Services/whatsappservice.php',
    __DIR__ . '/../services/whatsappservice.php'
];

$carregouWhats = false;
foreach ($whatsPaths as $wPath) {
    if (file_exists($wPath)) {
        require_once $wPath;
        $carregouWhats = true;
        break;
    }
}

// Fallback defensivo: se o arquivo não estiver no servidor, define a classe provisória para a página não quebrar
if (!$carregouWhats && !class_exists('WhatsAppService')) {
    class WhatsAppService {
        public static function gerarLinkStatus(array $p, string $l): string {
            $num = preg_replace('/\D/', '', $p['telefone'] ?? '');
            return "https://api.whatsapp.com/send?phone=55{$num}&text=" . urlencode("Status do seu pedido #{$p['codigo_pedido']} atualizado no {$l}!");
        }
    }
}


class LojistaController {

    private PDO $db;

    public function __construct() {
        $this->db = Conexao::getConexao();
    }

    /**
     * Dashboard Principal com Kanban de Pedidos & Faturamento
     */
     /**
     * Dashboard Principal com Kanban Otimizado & Histórico
     */
    public function dashboard(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        // Ações de alteração de status do Pedido via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_pedido'])) {
            $pedidoId = (int)$_POST['pedido_id'];
            $novoStatus = trim($_POST['novo_status']);

            $up = $db->prepare("UPDATE ws_pedidos SET status = ? WHERE id = ? AND user_id = ?");
            $up->execute([$novoStatus, $pedidoId, $userId]);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $stmtP = $db->prepare("SELECT * FROM ws_pedidos WHERE id = ?");
                $stmtP->execute([$pedidoId]);
                $pedAtualizado = $stmtP->fetch(PDO::FETCH_ASSOC);

                $linkWhats = WhatsAppService::gerarLinkStatus($pedAtualizado, $loja['user_name']);
                header('Content-Type: application/json');
                echo json_encode(['sucesso' => true, 'link_whatsapp' => $linkWhats]);
                exit;
            }

            header("Location: /" . $loja['user_virtual_env'] . "/admin");
            exit;
        }

        // Endpoint AJAX para buscar histórico com filtros (Data, Status e Forma de Pagamento)
        if (isset($_GET['ajax_historico'])) {
            header('Content-Type: application/json');
            $dtIni    = !empty($_GET['dt_ini']) ? $_GET['dt_ini'] . ' 00:00:00' : date('Y-m-01 00:00:00');
            $dtFim    = !empty($_GET['dt_fim']) ? $_GET['dt_fim'] . ' 23:59:59' : date('Y-m-d 23:59:59');
            $stFiltro = trim($_GET['status_hist'] ?? '');
            $pgFiltro = trim($_GET['pgto_hist'] ?? '');

            $sqlH = "SELECT * FROM ws_pedidos WHERE user_id = ? AND data BETWEEN ? AND ?";
            $paramsH = [$userId, $dtIni, $dtFim];

            if (!empty($stFiltro)) {
                $sqlH .= " AND LOWER(status) = LOWER(?)";
                $paramsH[] = $stFiltro;
            }

            // NOVO FILTRO: Forma de Pagamento
            if (!empty($pgFiltro)) {
                $sqlH .= " AND LOWER(forma_pagamento) = LOWER(?)";
                $paramsH[] = $pgFiltro;
            }

            $sqlH .= " ORDER BY data DESC LIMIT 500";
            $stmtH = $db->prepare($sqlH);
            $stmtH->execute($paramsH);
            $historico = $stmtH->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($historico);
            exit;
        }

        // Busca formas de pagamento cadastradas pelo lojista (ws_formas_pagamento)
        $stmtFormasPgto = $db->prepare("SELECT id_f_pagamento, f_pagamento FROM ws_formas_pagamento WHERE user_id = ? ORDER BY f_pagamento ASC");
        $stmtFormasPgto->execute([$userId]);
        $formasPagamentoLojista = $stmtFormasPgto->fetchAll(PDO::FETCH_ASSOC);

        // Filtros da Tela Principal
        $filtroStatus = trim($_GET['status'] ?? '');
        $dataInicio   = trim($_GET['data_inicio'] ?? '');
        $dataFim      = trim($_GET['data_fim'] ?? '');
        $filtroBusca  = trim($_GET['busca'] ?? '');

        $sql = "SELECT * FROM ws_pedidos WHERE user_id = ?";
        $params = [$userId];

        if (!empty($filtroStatus)) {
            $sql .= " AND LOWER(status) = LOWER(?)";
            $params[] = $filtroStatus;
        }
        if (!empty($dataInicio) && !empty($dataFim)) {
            $sql .= " AND data BETWEEN ? AND ?";
            $params[] = $dataInicio . ' 00:00:00';
            $params[] = $dataFim . ' 23:59:59';
        } elseif (!empty($dataInicio)) {
            $sql .= " AND data >= ?";
            $params[] = $dataInicio . ' 00:00:00';
        } elseif (!empty($dataFim)) {
            $sql .= " AND data <= ?";
            $params[] = $dataFim . ' 23:59:59';
        }

        if (!empty($filtroBusca)) {
            $sql .= " AND (codigo_pedido LIKE ? OR nome LIKE ? OR telefone LIKE ?)";
            $params[] = "%{$filtroBusca}%";
            $params[] = "%{$filtroBusca}%";
            $params[] = "%{$filtroBusca}%";
        }

        $sql .= " ORDER BY id DESC LIMIT 150";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupamento para as 4 Fases
        $faseRecebido   = [];
        $faseAndamento  = [];
        $faseEntrega    = [];
        $faseFinalizado = [];

        $hoje = date('Y-m-d');

        foreach ($pedidos as $p) {
            $st = strtolower(trim($p['status']));
            $dataPed = date('Y-m-d', strtotime($p['data']));

            if ($st === 'aberto' || $st === 'recebido') {
                $faseRecebido[] = $p;
            } elseif ($st === 'em andamento' || $st === 'em preparo') {
                $faseAndamento[] = $p;
            } elseif ($st === 'saiu para entrega' || $st === 'em rota') {
                $faseEntrega[] = $p;
            } else {
                if ($dataPed === $hoje || !empty($dataInicio)) {
                    $faseFinalizado[] = $p;
                }
            }
        }

        // Estatísticas
        $stmtVendasHoje = $db->prepare("SELECT COALESCE(SUM(total), 0) as total, COUNT(*) as qtd FROM ws_pedidos WHERE user_id = ? AND DATE(data) = CURDATE()");
        $stmtVendasHoje->execute([$userId]);
        $statsHoje = $stmtVendasHoje->fetch(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/dashboard.php";
    }

    /**
     * Gestão de Produtos (ws_itens)  com suporte a AJAX
     */
    
    public function produtos(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        // Processamento de Ações via POST (Salvar, Editar, Excluir)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? '';
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

            try {
                if ($acao === 'salvar') {
                    $nome  = trim($_POST['nome_item'] ?? '');
                    $desc  = trim($_POST['descricao_item'] ?? '');
                    $preco = (float)str_replace(',', '.', $_POST['preco_item'] ?? '0');
                    $catId = (int)($_POST['id_cat'] ?? 0);
                    $disp  = isset($_POST['disponivel']) ? 1 : 0;
                    $id    = (int)($_POST['id_item'] ?? 0);

                    // Upload de imagem opcional
                    $imgSql = "";
                    $imgParams = [];
                    if (!empty($_FILES['imagem']['name'])) {
                        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                        $novoNome = uniqid('prod_' . $userId . '_') . '.' . $ext;
                        $dest = __DIR__ . '/../../uploads/' . $novoNome;
                        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dest)) {
                            $imgSql = ", img_item = ?";
                            $imgParams[] = $novoNome;
                        }
                    }

                    if ($id > 0) {
                        // EDIÇÃO
                        $sql = "UPDATE ws_itens SET id_cat = ?, nome_item = ?, descricao_item = ?, preco_item = ?, disponivel = ? {$imgSql} WHERE id = ? AND user_id = ?";
                        $params = array_merge([$catId, $nome, $desc, $preco, $disp], $imgParams, [$id, $userId]);
                        $stmt = $db->prepare($sql);
                        $stmt->execute($params);
                        $msg = "Produto \"{$nome}\" atualizado com sucesso!";
                    } else {
                        // CADASTRO NOVO
                        $imgFinal = !empty($imgParams) ? $imgParams[0] : 'false';
                        $sql = "INSERT INTO ws_itens (user_id, id_cat, nome_item, descricao_item, preco_item, img_item, disponivel, dia_semana, number_adicional) VALUES (?, ?, ?, ?, ?, ?, ?, 'Domingo,Segunda,Terça,Quarta,Quinta,Sexta,Sabado', 0)";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([$userId, $catId, $nome, $desc, $preco, $imgFinal, $disp]);
                        $msg = "Produto \"{$nome}\" cadastrado com sucesso!";
                    }

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);
                        exit;
                    }
                }

                if ($acao === 'excluir') {
                    $id = (int)$_POST['id'];
                    $stmtNome = $db->prepare("SELECT nome_item FROM ws_itens WHERE id = ? AND user_id = ?");
                    $stmtNome->execute([$id, $userId]);
                    $prodNome = $stmtNome->fetchColumn() ?: 'Item';

                    $del = $db->prepare("DELETE FROM ws_itens WHERE id = ? AND user_id = ?");
                    $del->execute([$id, $userId]);

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['sucesso' => true, 'mensagem' => "Produto \"{$prodNome}\" excluído com sucesso!"]);
                        exit;
                    }
                }
            } catch (Exception $e) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
                    exit;
                }
            }

            header("Location: /" . $loja['user_virtual_env'] . "/admin?secao=produtos");
            exit;
        }

        // Listagem de Categorias e Produtos
        $stmtCats = $db->prepare("SELECT id, nome_cat FROM ws_cat WHERE user_id = ? ORDER BY nome_cat ASC");
        $stmtCats->execute([$userId]);
        $categorias = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

        $stmtProds = $db->prepare("SELECT i.*, c.nome_cat FROM ws_itens i LEFT JOIN ws_cat c ON c.id = i.id_cat WHERE i.user_id = ? ORDER BY i.id DESC");
        $stmtProds->execute([$userId]);
        $produtos = $stmtProds->fetchAll(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/produtos.php";
    }

    /**
     * Gestão de Categorias (ws_cat)
     */
    /**
     * Gestão de Categorias (ws_cat) com suporte a AJAX
     */
    public function categorias(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? '';
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

            try {
                if ($acao === 'salvar') {
                    $nome = trim($_POST['nome_cat'] ?? '');
                    $desc = trim($_POST['desc_cat'] ?? '');
                    $id   = (int)($_POST['id_cat'] ?? 0);

                    if ($id > 0) {
                        $stmt = $db->prepare("UPDATE ws_cat SET nome_cat = ?, desc_cat = ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$nome, $desc, $id, $userId]);
                        $msg = "Categoria \"{$nome}\" atualizada com sucesso!";
                    } else {
                        $stmt = $db->prepare("INSERT INTO ws_cat (user_id, nome_cat, desc_cat, icon_cat) VALUES (?, ?, ?, 'img/burger.png')");
                        $stmt->execute([$userId, $nome, $desc]);
                        $msg = "Categoria \"{$nome}\" cadastrada com sucesso!";
                    }

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);
                        exit;
                    }
                }

                if ($acao === 'excluir') {
                    $id = (int)$_POST['id'];
                    $stmtNome = $db->prepare("SELECT nome_cat FROM ws_cat WHERE id = ? AND user_id = ?");
                    $stmtNome->execute([$id, $userId]);
                    $catNome = $stmtNome->fetchColumn() ?: 'Categoria';

                    $del = $db->prepare("DELETE FROM ws_cat WHERE id = ? AND user_id = ?");
                    $del->execute([$id, $userId]);

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['sucesso' => true, 'mensagem' => "Categoria \"{$catNome}\" excluída com sucesso!"]);
                        exit;
                    }
                }
            } catch (Exception $e) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
                    exit;
                }
            }

            header("Location: /" . $loja['user_virtual_env'] . "/admin?secao=categorias");
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM ws_cat WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/categorias.php";
    }


    /**
     * Gestão de Taxas de Entrega / Bairros (bairros_delivery)
     */
    public function taxas(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? '';

            if ($acao === 'salvar') {
                $bairro = trim($_POST['bairro']);
                $cidade = trim($_POST['cidade'] ?? 'Minha Cidade');
                $uf     = strtoupper(trim($_POST['uf'] ?? 'SP'));
                $taxa   = (float)str_replace(',', '.', $_POST['taxa']);

                $stmt = $db->prepare("INSERT INTO bairros_delivery (user_id, uf, cidade, bairro, taxa) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $uf, $cidade, $bairro, $taxa]);
            }

            if ($acao === 'excluir') {
                $id = (int)$_POST['id'];
                $del = $db->prepare("DELETE FROM bairros_delivery WHERE id = ? AND user_id = ?");
                $del->execute([$id, $userId]);
            }

            header("Location: /" . $loja['user_virtual_env'] . "/admin?pagina=taxas");
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM bairros_delivery WHERE user_id = ? ORDER BY bairro ASC");
        $stmt->execute([$userId]);
        $bairros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/taxas.php";
    }

    /**
     * Gestão de Horários de Funcionamento (ws_empresa)
     */
    public function horarios(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
            $sets = [];
            $vals = [];

            foreach ($dias as $dia) {
                $status = isset($_POST["status_{$dia}"]) ? 'on' : 'off';
                $inicio = $_POST["inicio_{$dia}"] ?? '18:00';
                $fim    = $_POST["fim_{$dia}"] ?? '23:59';
                $campo  = "config_{$dia}";

                $sets[] = "{$campo} = ?";
                $vals[] = "{$status} {$inicio}-{$fim}";
            }

            $vals[] = $userId;
            $sql = "UPDATE ws_empresa SET " . implode(', ', $sets) . " WHERE user_id = ?";
            $up = $db->prepare($sql);
            $up->execute($vals);

            header("Location: /" . $loja['user_virtual_env'] . "/admin?pagina=horarios&sucesso=1");
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM ws_empresa WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/horarios.php";
    }


    /**
     * Gestão de Configurações da Loja (ws_empresa)
     * Logo, Capa, WhatsApp, Nome, Endereço, Textos de Delivery
     */
    public function configuracoes(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeEmpresa   = trim($_POST['nome_empresa'] ?? '');
            $descricao     = trim($_POST['descricao_empresa'] ?? '');
            $telefone      = trim($_POST['telefone_empresa'] ?? '');
            $email         = trim($_POST['email_empresa'] ?? '');
            $rua           = trim($_POST['end_rua_n_empresa'] ?? '');
            $bairro        = trim($_POST['end_bairro_empresa'] ?? '');
            $cidade        = trim($_POST['cidade_empresa'] ?? '');
            $uf            = trim($_POST['end_uf_empresa'] ?? 'SP');
            $tempoDelivery = trim($_POST['msg_tempo_delivery'] ?? '30-60 min');
            $minimoPed     = (float)str_replace(',', '.', $_POST['minimo_delivery'] ?? '0');
            $taxaPadrao    = (float)str_replace(',', '.', $_POST['config_delivery'] ?? '0');

            // Busca empresa atual para manter imagens se não houver upload novo
            $stmtAtual = $db->prepare("SELECT img_logo, img_header FROM ws_empresa WHERE user_id = ? LIMIT 1");
            $stmtAtual->execute([$userId]);
            $empAtual = $stmtAtual->fetch(PDO::FETCH_ASSOC);

            $imgLogo = $empAtual['img_logo'] ?? 'logo_default.png';
            $imgCapa = $empAtual['img_header'] ?? 'capa_default.png';

            // Upload de Logo
            if (!empty($_FILES['img_logo']['name'])) {
                $ext = strtolower(pathinfo($_FILES['img_logo']['name'], PATHINFO_EXTENSION));
                $nomeArq = 'logo_' . $userId . '_' . time() . '.' . $ext;
                $dest = __DIR__ . '/../../uploads/' . $nomeArq;
                if (move_uploaded_file($_FILES['img_logo']['tmp_name'], $dest)) {
                    $imgLogo = $nomeArq;
                }
            }

            // Upload de Capa / Header
            if (!empty($_FILES['img_header']['name'])) {
                $ext = strtolower(pathinfo($_FILES['img_header']['name'], PATHINFO_EXTENSION));
                $nomeArq = 'capa_' . $userId . '_' . time() . '.' . $ext;
                $dest = __DIR__ . '/../../uploads/' . $nomeArq;
                if (move_uploaded_file($_FILES['img_header']['tmp_name'], $dest)) {
                    $imgCapa = $nomeArq;
                }
            }

            // Atualiza ou Insere na ws_empresa
            $check = $db->prepare("SELECT id_empresa FROM ws_empresa WHERE user_id = ?");
            $check->execute([$userId]);
            if ($check->rowCount() > 0) {
                $sql = "UPDATE ws_empresa SET 
                            nome_empresa = ?, descricao_empresa = ?, telefone_empresa = ?, email_empresa = ?,
                            end_rua_n_empresa = ?, end_bairro_empresa = ?, cidade_empresa = ?, end_uf_empresa = ?,
                            msg_tempo_delivery = ?, minimo_delivery = ?, config_delivery = ?,
                            img_logo = ?, img_header = ?
                        WHERE user_id = ?";
                $up = $db->prepare($sql);
                $up->execute([
                    $nomeEmpresa, $descricao, $telefone, $email,
                    $rua, $bairro, $cidade, $uf,
                    $tempoDelivery, $minimoPed, $taxaPadrao,
                    $imgLogo, $imgCapa, $userId
                ]);
            } else {
                $sql = "INSERT INTO ws_empresa (
                            user_id, nome_empresa, descricao_empresa, telefone_empresa, email_empresa,
                            end_rua_n_empresa, end_bairro_empresa, cidade_empresa, end_uf_empresa,
                            msg_tempo_delivery, minimo_delivery, config_delivery, img_logo, img_header,
                            nome_empresa_link, config_segunda, config_terca, config_quarta, config_quinta, config_sexta, config_sabado, config_domingo
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'on 18:00-23:59', 'on 18:00-23:59', 'on 18:00-23:59', 'on 18:00-23:59', 'on 18:00-23:59', 'on 18:00-23:59', 'on 18:00-23:59')";
                $ins = $db->prepare($sql);
                $ins->execute([
                    $userId, $nomeEmpresa, $descricao, $telefone, $email,
                    $rua, $bairro, $cidade, $uf,
                    $tempoDelivery, $minimoPed, $taxaPadrao, $imgLogo, $imgCapa,
                    $loja['user_virtual_env']
                ]);
            }

            header("Location: /" . $loja['user_virtual_env'] . "/admin?secao=configuracoes&sucesso=1");
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM ws_empresa WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/configuracoes.php";
    }

    /**
     * CRM de Clientes (ws_clientes)
     * Filtros: Todos, Inativos (sem pedir há X dias), Aniversariantes do Dia e da Semana
     */
    public function clientes(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = $this->db;

        $filtro = trim($_GET['filtro'] ?? 'todos');

        // Métricas Gerais de CRM
        $totalClientes = (int)$db->query("SELECT COUNT(*) FROM ws_clientes WHERE user_id_loja = {$userId}")->fetchColumn();

        // Aniversariantes do dia
        $aniversariantesDia = (int)$db->query("SELECT COUNT(*) FROM ws_clientes WHERE user_id_loja = {$userId} AND DATE_FORMAT(data_nascimento, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')")->fetchColumn();

        // Aniversariantes da semana
        $aniversariantesSemana = (int)$db->query("SELECT COUNT(*) FROM ws_clientes WHERE user_id_loja = {$userId} AND WEEK(data_nascimento, 1) = WEEK(CURDATE(), 1)")->fetchColumn();

        // Clientes Inativos (+ de 30 dias sem pedir)
        $inativos30Dias = (int)$db->query("SELECT COUNT(*) FROM ws_clientes WHERE user_id_loja = {$userId} AND (ultima_visita < DATE_SUB(CURDATE(), INTERVAL 30 DAY) OR ultima_visita IS NULL)")->fetchColumn();

        // Consulta filtrada para a tabela
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM ws_pedidos p WHERE p.user_id = c.user_id_loja AND (p.cliente_id = c.cliente_id OR p.telefone = c.telefone)) as total_pedidos,
                (SELECT COALESCE(SUM(total), 0) FROM ws_pedidos p WHERE p.user_id = c.user_id_loja AND (p.cliente_id = c.cliente_id OR p.telefone = c.telefone)) as total_gasto
                FROM ws_clientes c 
                WHERE c.user_id_loja = ?";

        if ($filtro === 'aniversariantes_dia') {
            $sql .= " AND DATE_FORMAT(c.data_nascimento, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";
        } elseif ($filtro === 'aniversariantes_semana') {
            $sql .= " AND WEEK(c.data_nascimento, 1) = WEEK(CURDATE(), 1)";
        } elseif ($filtro === 'inativos_30') {
            $sql .= " AND (c.ultima_visita < DATE_SUB(CURDATE(), INTERVAL 30 DAY) OR c.ultima_visita IS NULL)";
        }

        $sql .= " ORDER BY c.cliente_id DESC LIMIT 200";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/clientes.php";
    }


    /**
     * Gestão de Usuários da Equipe da Loja (ws_users)
     * Limite: Até 3 usuários por lanchonete (o titular + 2 adicionais)
     */
    public function usuarios(array $loja): void {
        $slug = trim($loja['user_virtual_env']);
        $db = $this->db;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? '';
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

            try {
                if ($acao === 'salvar') {
                    $nome        = trim($_POST['user_name'] ?? '');
                    $cpf         = trim($_POST['user_cpf'] ?? '');
                    $telefone    = trim($_POST['user_telefone'] ?? '');
                    $email       = trim($_POST['user_email'] ?? '');
                    $senha       = trim($_POST['user_password'] ?? '');
                    $confirma    = trim($_POST['user_password_confirm'] ?? '');
                    $nivel       = (int)($_POST['user_level'] ?? 1); // 1 = Comum, 2 = Motoboy, 3 = Admin
                    $id          = (int)($_POST['user_id'] ?? 0);

                    if (empty($nome) || empty($email)) {
                        throw new Exception("Nome e E-mail são obrigatórios!");
                    }

                    if ($id > 0) {
                        // EDIÇÃO DE USUÁRIO
                        $chkEmail = $db->prepare("SELECT user_id FROM ws_users WHERE LOWER(user_email) = LOWER(?) AND user_id != ?");
                        $chkEmail->execute([$email, $id]);
                        if ($chkEmail->rowCount() > 0) {
                            throw new Exception("Este e-mail já está em uso por outro usuário!");
                        }

                        $senhaSql = "";
                        $params = [$nome, $cpf, $telefone, $email, $nivel];

                        if (!empty($senha)) {
                            if ($senha !== $confirma) {
                                throw new Exception("A senha e a confirmação de senha não coincidem!");
                            }
                            $senhaSql = ", user_password = ?";
                            $params[] = Auth::hashSenha($senha);
                        }

                        $params[] = $id;
                        $params[] = $slug;

                        $sql = "UPDATE ws_users SET user_name = ?, user_cpf = ?, user_telefone = ?, user_email = ?, user_level = ? {$senhaSql} WHERE user_id = ? AND user_virtual_env = ?";
                        $stmt = $db->prepare($sql);
                        $stmt->execute($params);
                        $msg = "Usuário \"{$nome}\" atualizado com sucesso!";
                    } else {
                        // CADASTRO NOVO: Checa limite de 3 usuários
                        $stmtContagem = $db->prepare("SELECT COUNT(*) FROM ws_users WHERE user_virtual_env = ?");
                        $stmtContagem->execute([$slug]);
                        $totalUsuariosLoja = (int)$stmtContagem->fetchColumn();

                        if ($totalUsuariosLoja >= 3) {
                            throw new Exception("Limite atingido! Sua loja já possui {$totalUsuariosLoja} usuários cadastrados (limite de 3).");
                        }

                        // Valida se o e-mail já existe
                        $chkEmail = $db->prepare("SELECT user_id FROM ws_users WHERE LOWER(user_email) = LOWER(?)");
                        $chkEmail->execute([$email]);
                        if ($chkEmail->rowCount() > 0) {
                            throw new Exception("Este e-mail já está cadastrado no sistema!");
                        }

                        if (empty($senha)) {
                            throw new Exception("A senha é obrigatória para novos cadastros!");
                        }

                        if ($senha !== $confirma) {
                            throw new Exception("A senha e a confirmação de senha não coincidem!");
                        }

                        $senhaHash = Auth::hashSenha($senha);

                        // Grava herdando o plano e com user_status = 'ativo' para garantir login
                        $sql = "INSERT INTO ws_users (
                                    user_name, user_lastname, user_virtual_env, user_email, user_cpf,
                                    user_telefone, user_img_perfil, user_password, user_level, user_plano,
                                    user_status, user_cont, user_nome_plano, user_dias_plano, status_assinatura_plano,
                                    codigo_assinante, user_data_renova
                                ) VALUES (
                                    ?, '', ?, ?, ?,
                                    ?, '', ?, ?, 1,
                                    'ativo', 0, '', 0, '',
                                    '', '2029-12-31'
                                )";

                        $stmt = $db->prepare($sql);
                        $stmt->execute([$nome, $slug, $email, $cpf, $telefone, $senhaHash, $nivel]);
                        $msg = "Usuário \"{$nome}\" cadastrado com sucesso! Já pode realizar login.";
                    }

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['sucesso' => true, 'mensagem' => $msg]);
                        exit;
                    }
                }

                if ($acao === 'excluir') {
                    $id = (int)$_POST['id'];

                    if ($id === (int)$loja['user_id']) {
                        throw new Exception("Você não pode excluir o usuário proprietário da loja!");
                    }

                    $del = $db->prepare("DELETE FROM ws_users WHERE user_id = ? AND user_virtual_env = ?");
                    $del->execute([$id, $slug]);

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['sucesso' => true, 'mensagem' => "Usuário removido da equipe com sucesso!"]);
                        exit;
                    }
                }
            } catch (Exception $e) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
                    exit;
                }
            }

            header("Location: /" . $loja['user_virtual_env'] . "/admin?secao=usuarios");
            exit;
        }

        // Listagem de usuários
        $stmtUsers = $db->prepare("SELECT user_id, user_name, user_cpf, user_telefone, user_email, user_level, user_status, user_registration, user_ultimoacesso FROM ws_users WHERE user_virtual_env = ? ORDER BY user_id ASC");
        $stmtUsers->execute([$slug]);
        $usuarios = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        $pastaLogista = is_dir(__DIR__ . '/../../views/admin_logista') ? 'admin_logista' : 'admin_lojista';
        require_once __DIR__ . "/../../views/{$pastaLogista}/usuarios.php";
    }
}