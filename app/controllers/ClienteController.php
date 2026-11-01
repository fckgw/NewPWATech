<?php

require_once __DIR__ . '/../Database/Conexao.php';
require_once __DIR__ . '/../Security/Auth.php';

class ClienteController {

    private PDO $db;

    public function __construct() {
        $this->db = Conexao::getConexao();
    }

    /**
     * Cadastro Rápido do Cliente
     * Valida se o email já existe em ws_clientes OU em ws_users (donos de loja)
     */
    public function registrar(array $loja): void {
        $lojaId = (int)$loja['user_id'];
        $slug   = $loja['user_virtual_env'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome       = trim($_POST['nome_completo'] ?? '');
            $telefone   = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
            $nascimento = trim($_POST['data_nascimento'] ?? '');
            $email      = trim($_POST['email'] ?? '');

            // Campos estritamente obrigatórios
            if (empty($nome) || empty($telefone) || empty($nascimento)) {
                $_SESSION['flash_ErroCadastro'] = "Preencha todos os campos obrigatórios!";
                header("Location: /{$slug}");
                exit;
            }

            // Se o e-mail foi preenchido, valida duplicidade
            if (!empty($email)) {
                // 1. Verifica se já existe em ws_users (Lojistas/Admins)
                $stmtCheckUser = $this->db->prepare("SELECT user_id FROM ws_users WHERE TRIM(LOWER(user_email)) = TRIM(LOWER(?)) LIMIT 1");
                $stmtCheckUser->execute([$email]);
                if ($stmtCheckUser->rowCount() > 0) {
                    $_SESSION['flash_ErroCadastro'] = "Este e-mail pertence a um administrador do sistema e não pode ser usado como cliente!";
                    $_SESSION['flash_TelefoneTentativa'] = $_POST['telefone'] ?? '';
                    header("Location: /{$slug}");
                    exit;
                }

                // 2. Verifica se já existe em ws_clientes
                $stmtCheckCli = $this->db->prepare("SELECT cliente_id FROM ws_clientes WHERE TRIM(LOWER(email)) = TRIM(LOWER(?)) LIMIT 1");
                $stmtCheckCli->execute([$email]);
                if ($stmtCheckCli->rowCount() > 0) {
                    $_SESSION['flash_ErroCadastro'] = "Este e-mail já está cadastrado por outro cliente!";
                    $_SESSION['flash_TelefoneTentativa'] = $_POST['telefone'] ?? '';
                    header("Location: /{$slug}");
                    exit;
                }
            }

            // Senha Padrão C#: Telefone puro + Ano do nascimento
            $anoNasc = date('Y', strtotime($nascimento));
            $senhaPadrao = $telefone . $anoNasc;
            $senhaHash = Auth::hashSenha($senhaPadrao);

            // Verifica se o telefone já está cadastrado nesta loja
            $stmtVer = $this->db->prepare("SELECT cliente_id FROM ws_clientes WHERE user_id_loja = ? AND telefone = ? LIMIT 1");
            $stmtVer->execute([$lojaId, $telefone]);
            $clienteExiste = $stmtVer->fetch(PDO::FETCH_ASSOC);

            if ($clienteExiste) {
                $clienteId = (int)$clienteExiste['cliente_id'];
            } else {
                $stmtIns = $this->db->prepare("INSERT INTO ws_clientes (user_id_loja, nome_completo, telefone, data_nascimento, email, senha, ultima_visita, data_registro, trocar_senha) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), 1)");
                $stmtIns->execute([$lojaId, $nome, $telefone, $nascimento, !empty($email) ? $email : null, $senhaHash]);
                $clienteId = (int)$this->db->lastInsertId();
            }

            // Inicia Sessão do Consumidor
            $_SESSION['Cliente_ID']           = $clienteId;
            $_SESSION['Cliente_Nome']         = $nome;
            $_SESSION['Cliente_Telefone']     = $telefone;
            $_SESSION['Cliente_UltimoAcesso'] = date('d/m/Y H:i');
            $_SESSION['flash_ShowWelcome']    = true;

            header("Location: /{$slug}");
            exit;
        }
    }

    /**
     * Salva o Pedido na Base (ws_pedidos) via AJAX e gera o link do WhatsApp
     */
    public function salvarPedido(array $loja): void {
        // Limpa qualquer saída anterior para garantir JSON puro
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
            exit;
        }

        $userId = (int)$loja['user_id'];
        $dadosJson = file_get_contents('php://input');
        $dados = json_decode($dadosJson, true);

        if (!$dados || empty($dados['itens'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Carrinho vazio ou dados inválidos']);
            exit;
        }

        try {
            $clienteId  = !empty($_SESSION['Cliente_ID']) ? (int)$_SESSION['Cliente_ID'] : null;
            $nome       = trim($dados['nome'] ?? 'Cliente');
            $telefone   = preg_replace('/\D/', '', $dados['telefone'] ?? '');
            $rua        = trim($dados['rua'] ?? '');
            
            // Tratamento rigoroso para 'unidade' (campo int(11) NOT NULL no MySQL)
            $numeroRaw  = preg_replace('/\D/', '', $dados['numero'] ?? '');
            $unidade    = !empty($numeroRaw) ? (int)$numeroRaw : 0;

            $bairro     = trim($dados['bairro_nome'] ?? '');
            $cidade     = trim($dados['cidade'] ?? 'Caçapava');
            $uf         = trim($dados['uf'] ?? 'SP');
            $pgto       = trim($dados['forma_pagamento'] ?? 'Dinheiro');
            $taxa       = (float)($dados['taxa_entrega'] ?? 0);
            $subtotal   = (float)($dados['subtotal'] ?? 0);
            $total      = (float)($dados['total'] ?? 0);

            // Código do pedido único no padrão da tabela (ex: PED1126-4821)
            $codigoPedido = 'PED' . date('my') . '-' . rand(100, 9999);

            // Monta o resumo_pedidos formatado em HTML igual ao dump do banco
            $resumoHtml = "";
            foreach ($dados['itens'] as $item) {
                $itemNome = htmlspecialchars($item['nome']);
                $itemQtd  = (int)($item['quantidade'] ?? 1);
                $itemVal  = number_format($item['totalItem'], 2, ',', '.');
                $itemObs  = !empty($item['obs']) ? htmlspecialchars($item['obs']) : 'Não';
                $adicTxt  = "Sem Adicionais";

                if (!empty($item['adicionais'])) {
                    $adicTxt = implode(', ', array_map(function($a) {
                        return "{$a['qtd']}x " . htmlspecialchars($a['nome']);
                    }, $item['adicionais']));
                }

                $resumoHtml .= "<b>Qtd:</b> {$itemQtd}x {$itemNome}<br />";
                $resumoHtml .= "<b>Adicionais:</b> {$adicTxt}<br />";
                $resumoHtml .= "<b>Valor:</b> R$ {$itemVal}<br />";
                $resumoHtml .= "<b>OBS:</b> {$itemObs}<br /><br />";
            }

            // Grava endereço se cliente logado
            if ($clienteId && !empty($rua)) {
                try {
                    $stmtEnd = $this->db->prepare("INSERT INTO ws_clientes_enderecos (cliente_id, rua, numero, bairro, cidade, uf) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmtEnd->execute([$clienteId, $rua, (string)$unidade, $bairro, $cidade, $uf]);
                } catch (Exception $eEnd) {
                    // Ignora erro secundário de endereço para não travar o pedido
                }
            }

            // INSERT EXATO NAS COLUNAS DE ws_pedidos
            $sql = "INSERT INTO ws_pedidos (
                        user_id, cliente_id, codigo_pedido, data, data_chart, data_chart2,
                        resumo_pedidos, forma_pagamento, valor_troco, opcao_delivery, valor_taxa,
                        telefone_empresa, adicionais, sub_total, total, nome, telefone,
                        rua, unidade, bairro, cidade, uf, complemento, observacao,
                        name_observacao_mesa, status, mes, ano, view, desconto, confirm_whatsapp, msg_delivery_false
                    ) VALUES (
                        ?, ?, ?, NOW(), DATE_FORMAT(NOW(), '%Y-%m'), CURDATE(),
                        ?, ?, 0.00, 'true', ?,
                        ?, '0', ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, '', '',
                        '', 'Aberto', DATE_FORMAT(NOW(), '%m'), DATE_FORMAT(NOW(), '%y'), 0, 0, 'false', ''
                    )";

            $stmtPed = $this->db->prepare($sql);
            $telEmpresa = $loja['user_telefone'] ?? '';

            $stmtPed->execute([
                $userId,
                $clienteId,
                $codigoPedido,
                $resumoHtml,
                $pgto,
                $taxa,
                $telEmpresa,
                $subtotal,
                $total,
                $nome,
                $telefone,
                $rua,
                $unidade,
                $bairro,
                $cidade,
                $uf
            ]);

            $pedidoId = (int)$this->db->lastInsertId();

            echo json_encode([
                'sucesso'       => true,
                'pedido_id'     => $pedidoId,
                'codigo_pedido' => $codigoPedido,
                'mensagem'      => 'Pedido gerado com sucesso!'
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (PDOException $e) {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => 'Erro de banco de dados: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'sucesso'  => false,
                'mensagem' => 'Erro geral: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function login(array $loja): void {
        $lojaId = (int)$loja['user_id'];
        $slug   = $loja['user_virtual_env'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $telefoneDigitado = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
            $senhaDigitada    = trim($_POST['senha'] ?? '');

            $stmt = $this->db->prepare("SELECT * FROM ws_clientes WHERE user_id_loja = ? AND telefone = ? LIMIT 1");
            $stmt->execute([$lojaId, $telefoneDigitado]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                $_SESSION['flash_UserNotFound']      = true;
                $_SESSION['flash_TelefoneTentativa'] = $_POST['telefone'] ?? '';
                header("Location: /{$slug}");
                exit;
            }

            $anoNasc = date('Y', strtotime($cliente['data_nascimento']));
            $senhaPadraoCalculada = $cliente['telefone'] . $anoNasc;

            if (Auth::verificarSenha($senhaDigitada, $cliente['senha']) || ($senhaDigitada === $senhaPadraoCalculada)) {
                $up = $this->db->prepare("UPDATE ws_clientes SET ultima_visita = NOW() WHERE cliente_id = ?");
                $up->execute([$cliente['cliente_id']]);

                $_SESSION['Cliente_ID']           = (int)$cliente['cliente_id'];
                $_SESSION['Cliente_Nome']         = $cliente['nome_completo'];
                $_SESSION['Cliente_Telefone']     = $cliente['telefone'];
                $_SESSION['Cliente_UltimoAcesso'] = date('d/m/Y H:i');
                $_SESSION['flash_ShowWelcome']    = true;

                header("Location: /{$slug}");
                exit;
            } else {
                $_SESSION['flash_WrongPassword'] = true;
                header("Location: /{$slug}");
                exit;
            }
        }
    }

    public function logout(array $loja): void {
        unset($_SESSION['Cliente_ID'], $_SESSION['Cliente_Nome'], $_SESSION['Cliente_Telefone'], $_SESSION['Cliente_UltimoAcesso']);
        header("Location: /" . $loja['user_virtual_env']);
        exit;
    }
}