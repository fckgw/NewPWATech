<?php

class HomeController {

    /**
     * Exibe o cardápio PWA para o cliente final
     * @param array|object $loja Dados do Tenant obtidos no index.php
     */
    public function cardapio($loja) {
        $empresa = is_array($loja) ? (object)$loja : $loja;
        $tenantId = $empresa->id ?? 0;

        // 1. Resolve dinamicamente a pasta de models (compatível com Linux models/ ou Models/)
        $modelsDir = is_dir(__DIR__ . '/../models') ? __DIR__ . '/../models' : __DIR__ . '/../Models';

        $models = ['Categoria', 'Produto', 'Adicional', 'BairroDelivery', 'FormaPagamento', 'EnderecoCliente', 'Empresa'];
        foreach ($models as $m) {
            if (file_exists("$modelsDir/$m.php")) {
                require_once "$modelsDir/$m.php";
            }
        }

        // 2. Conexão PDO segura (fallback se os Models ainda não tiverem sido implementados)
        $db = Conexao::getConexao();

        // Categorias
        if (class_exists('Categoria')) {
            $catModel = new Categoria();
            $categorias = $catModel->listarPorEmpresa($tenantId);
        } else {
            $stmt = $db->prepare("SELECT * FROM categorias WHERE tenant_id = ? ORDER BY ordem ASC");
            $stmt->execute([$tenantId]);
            $categorias = $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        // Produtos
        if (class_exists('Produto')) {
            $prodModel = new Produto();
            $produtos = $prodModel->listarAtivosPorEmpresa($tenantId);
        } else {
            $stmt = $db->prepare("SELECT * FROM produtos WHERE tenant_id = ? AND status = 1");
            $stmt->execute([$tenantId]);
            $produtos = $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        // Adicionais
        if (class_exists('Adicional')) {
            $adModel = new Adicional();
            $adicionais = $adModel->listarPorEmpresa($tenantId);
        } else {
            $stmt = $db->prepare("SELECT * FROM adicionais WHERE tenant_id = ?");
            $stmt->execute([$tenantId]);
            $adicionais = $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        // Bairros
        if (class_exists('BairroDelivery')) {
            $bairroModel = new BairroDelivery();
            $bairros = $bairroModel->listarPorEmpresa($tenantId);
        } else {
            $stmt = $db->prepare("SELECT * FROM bairros_delivery WHERE tenant_id = ?");
            $stmt->execute([$tenantId]);
            $bairros = $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        // Formas de Pagamento
        if (class_exists('FormaPagamento')) {
            $formaModel = new FormaPagamento();
            $pagamentos = $formaModel->listarPorEmpresa($tenantId);
        } else {
            $stmt = $db->prepare("SELECT * FROM formas_pagamento WHERE tenant_id = ?");
            $stmt->execute([$tenantId]);
            $pagamentos = $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        // Endereços do Cliente logado
        $enderecosSalvos = [];
        if (!empty($_SESSION['Cliente_ID'])) {
            if (class_exists('EnderecoCliente')) {
                $endModel = new EnderecoCliente();
                $enderecosSalvos = $endModel->listarPorCliente($_SESSION['Cliente_ID']);
            } else {
                $stmt = $db->prepare("SELECT * FROM enderecos_cliente WHERE cliente_id = ?");
                $stmt->execute([$_SESSION['Cliente_ID']]);
                $enderecosSalvos = $stmt->fetchAll(PDO::FETCH_CLASS);
            }
        }

        // Status aberto/fechado
        $estaAberto = true;
        if (class_exists('Empresa')) {
            $empModel = new Empresa();
            $estaAberto = $empModel->verificarSeEstaAberto($tenantId);
        } elseif (isset($empresa->aberto)) {
            $estaAberto = (bool)$empresa->aberto;
        }

        // Empacota os dados para a View
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

        // Renderiza a tela do PWA Cliente
        require_once __DIR__ . '/../../views/pwa_cliente/cardapio.php';
    }
}