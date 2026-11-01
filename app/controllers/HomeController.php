<?php

require_once __DIR__ . '/../Database/Conexao.php';

class HomeController {

    /**
     * Exibe o cardápio PWA para o cliente final
     * @param array $loja Registro de ws_users
     */
    public function cardapio(array $loja): void {
        $userId = (int)$loja['user_id'];
        $db = Conexao::getConexao();

        // 1. Dados da Empresa e Configurações de Horário (ws_empresa)
        $stmtEmp = $db->prepare("SELECT * FROM ws_empresa WHERE user_id = ? LIMIT 1");
        $stmtEmp->execute([$userId]);
        $empresaArr = $stmtEmp->fetch(PDO::FETCH_ASSOC);

        // Fallback caso ainda não tenha registro na ws_empresa para esta lanchonete
        if (!$empresaArr) {
            $empresaArr = [
                'id_empresa'        => 0,
                'nome_empresa'      => $loja['user_name'],
                'nome_empresa_link' => $loja['user_virtual_env'],
                'img_logo'          => 'logo-default.png',
                'img_header'        => 'header-default.png',
                'minimo_delivery'   => 0.00,
                'config_delivery'   => 0.00
            ];
        }
        $empresa = (object)$empresaArr;

        // 2. Categorias (ws_cat)
        $stmtCat = $db->prepare("SELECT id, nome_cat, desc_cat, icon_cat FROM ws_cat WHERE user_id = ? ORDER BY id ASC");
        $stmtCat->execute([$userId]);
        $categorias = $stmtCat->fetchAll(PDO::FETCH_CLASS);

        // 3. Produtos (ws_itens)
        $stmtProd = $db->prepare("SELECT id, id_cat, nome_item, descricao_item, preco_item, img_item, disponivel FROM ws_itens WHERE user_id = ? AND disponivel = 1 ORDER BY id DESC");
        $stmtProd->execute([$userId]);
        $produtos = $stmtProd->fetchAll(PDO::FETCH_CLASS);

        // 4. Adicionais de Itens (ws_adicionais_itens)
        $stmtAdic = $db->prepare("SELECT id_adicionais as Id, categorias_adicional, nome_adicional, valor_adicional, status_adicional FROM ws_adicionais_itens WHERE user_id = ? AND status_adicional = 1");
        $stmtAdic->execute([$userId]);
        $adicionais = $stmtAdic->fetchAll(PDO::FETCH_CLASS);

        // 5. Bairros e Taxas de Entrega (bairros_delivery)
        $stmtBairros = $db->prepare("SELECT id, bairro, taxa, cidade, uf FROM bairros_delivery WHERE user_id = ? ORDER BY bairro ASC");
        $stmtBairros->execute([$userId]);
        $bairros = $stmtBairros->fetchAll(PDO::FETCH_CLASS);

        // 6. Formas de Pagamento (ws_formas_pagamento)
        $stmtPg = $db->prepare("SELECT id_f_pagamento, f_pagamento FROM ws_formas_pagamento WHERE user_id = ?");
        $stmtPg->execute([$userId]);
        $pagamentos = $stmtPg->fetchAll(PDO::FETCH_CLASS);

        // 7. Endereços Salvos do Cliente Logado (ws_clientes_enderecos)
        $enderecosSalvos = [];
        if (!empty($_SESSION['Cliente_ID'])) {
            $stmtEnd = $db->prepare("SELECT * FROM ws_clientes_enderecos WHERE cliente_id = ? ORDER BY id_endereco DESC");
            $stmtEnd->execute([$_SESSION['Cliente_ID']]);
            $enderecosSalvos = $stmtEnd->fetchAll(PDO::FETCH_CLASS);
        }

        // 8. Checagem de Status Aberto / Fechado baseado no horário do dia atual
        $diasMap = [
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado',
            0 => 'domingo'
        ];
        $diaHoje = $diasMap[(int)date('w')];
        $campoHorario = "config_{$diaHoje}";
        $estaAberto = true;

        if (isset($empresaArr[$campoHorario])) {
            $configHoje = trim($empresaArr[$campoHorario]);
            if (str_starts_with($configHoje, 'off')) {
                $estaAberto = false;
            } elseif (str_starts_with($configHoje, 'on')) {
                $partes = explode(' ', $configHoje);
                if (isset($partes[1])) {
                    $faixas = explode('-', $partes[1]);
                    $horaAtual = date('H:i');
                    $horaIni = $faixas[0] ?? '00:00';
                    $horaFim = $faixas[1] ?? '23:59';
                    if ($horaAtual < $horaIni || $horaAtual > $horaFim) {
                        $estaAberto = false;
                    }
                }
            }
        }

        // Agrupamento para a View
        $data = [
            'empresa'         => $empresa,
            'categorias'      => $categorias,
            'produtos'        => $produtos,
            'adicionais'      => $adicionais,
            'bairros'         => $bairros,
            'pagamentos'      => $pagamentos,
            'enderecosSalvos' => $enderecosSalvos,
            'estaAberto'      => $estaAberto
        ];

        // Carrega a view universal do PWA
        require_once __DIR__ . '/../../views/pwa_cliente/cardapio.php';
    }
}