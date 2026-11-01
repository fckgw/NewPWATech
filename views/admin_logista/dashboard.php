<?php
// Carrega dados da empresa para o cabeçalho do cupom impresso
$stmtEmpCupom = $db->prepare("SELECT * FROM ws_empresa WHERE user_id = ? LIMIT 1");
$stmtEmpCupom->execute([(int)$loja['user_id']]);
$empresaCupom = $stmtEmpCupom->fetch(PDO::FETCH_ASSOC) ?: [];

$lojaNome     = $empresaCupom['nome_empresa'] ?? $loja['user_name'];
$lojaTelefone = $empresaCupom['telefone_empresa'] ?? ($loja['user_telefone'] ?? '');
$lojaEndereco = trim(($empresaCupom['end_rua_n_empresa'] ?? '') . ' - ' . ($empresaCupom['end_bairro_empresa'] ?? '') . ', ' . ($empresaCupom['cidade_empresa'] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel de Pedidos | <?= htmlspecialchars($lojaNome) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-body: #0e1726;
            --bg-card: #1b2e4b;
            --pwa-green: #34af23;
        }
        body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif; color: #fff; min-height: 100vh; overflow-x: hidden; }
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; border-right: 1px solid rgba(255,255,255,0.05); }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }

        .stat-card { background: var(--bg-card); border-radius: 20px; padding: 20px; border: 1px solid rgba(255,255,255,0.05); }

        /* KANBAN COM SCROLL VERTICAL INDEPENDENTE (NÃO QUEBRA A TELA) */
        .kanban-col {
            background: rgba(27, 46, 75, 0.4);
            border-radius: 20px;
            padding: 16px;
            height: calc(100vh - 290px);
            min-height: 550px;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .kanban-header {
            font-weight: 800;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .kanban-cards-wrapper {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }
        /* Scrollbar customizada moderna */
        .kanban-cards-wrapper::-webkit-scrollbar { width: 6px; }
        .kanban-cards-wrapper::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .kanban-cards-wrapper::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }
        .kanban-cards-wrapper::-webkit-scrollbar-thumb:hover { background: var(--pwa-green); }

        .order-card { background: var(--bg-card); border-radius: 16px; padding: 14px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.08); transition: 0.2s; position: relative; }
        .order-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.3); border-color: var(--pwa-green); }

        .btn-whatsapp { background: #25D366; color: #fff; font-weight: 700; border-radius: 10px; font-size: 0.8rem; padding: 7px 12px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-whatsapp:hover { background: #1eb956; color: #fff; }

        /* CUPOM TÉRMICO TOMATO 80MM */
        @media print {
            @page { margin: 0; size: 80mm auto; }
            html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; color: #000 !important; }
            body * { visibility: hidden; }
            #area-cupom-impressao, #area-cupom-impressao * { visibility: visible; }
            #area-cupom-impressao {
                position: absolute; left: 0; top: 0; width: 72mm; margin: 0 auto; padding: 4mm 2mm; font-family: 'Courier New', Courier, monospace; color: #000 !important; background: #fff !important;
            }
            .no-print { display: none !important; }
        }

        .cupom-termico { font-family: 'Courier New', Courier, monospace; font-size: 14px; font-weight: bold; color: #000; line-height: 1.25; text-transform: uppercase; }
        .cupom-termico .titulo-loja { font-size: 18px; font-weight: 900; text-align: center; margin-bottom: 3px; }
        .cupom-termico .divisor { border-top: 1px dashed #000; margin: 6px 0; }
        .cupom-termico .destaque { font-size: 16px; font-weight: 900; }
        .cupom-termico .itens-bloco { font-size: 14px; margin: 4px 0; word-break: break-word; }
    </style>
</head>
<body>

<div class="container-fluid no-print">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($lojaNome) ?></h5>
                    <span class="badge bg-success bg-opacity-20 text-success small mt-1">Loja Aberta</span>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin"><i class="fas fa-columns me-2"></i> Pedidos (Kanban)</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=categorias"><i class="fas fa-tags me-2"></i> Categorias</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes"><i class="fas fa-users me-2"></i> Meus Clientes (CRM)</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=configuracoes"><i class="fas fa-cog me-2"></i> Configurações</a>
                </nav>
            </div>

            <div>
                <a href="/<?= $loja['user_virtual_env'] ?>" target="_blank" class="btn btn-outline-success w-100 rounded-pill mb-2 fw-bold btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i> Abrir Cardápio
                </a>
                <a href="/<?= $loja['user_virtual_env'] ?>/admin?acao=logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">
                    <i class="fas fa-power-off me-1"></i> Sair do Painel
                </a>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL -->
        <main class="col-md-10 p-4">

            <!-- STATS SUPERIORES -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="stat-card">
                        <span class="text-secondary small fw-bold">TOTAL VENDIDO HOJE</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">R$ <?= number_format($statsHoje['total'], 2, ',', '.') ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <span class="text-secondary small fw-bold">PEDIDOS REALIZADOS HOJE</span>
                        <h3 class="fw-bold text-white mb-0 mt-1"><?= $statsHoje['qtd'] ?> pedidos</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <span class="text-secondary small fw-bold">LINK PÚBLICO DA LOJA</span>
                        <p class="text-info mb-0 mt-1 text-truncate fw-bold">pwa.bdsoft.com.br/<?= $loja['user_virtual_env'] ?></p>
                    </div>
                </div>
            </div>

            <!-- FILTRO DE BUSCA COM DATA INÍCIO E DATA FIM -->
            <div class="card bg-dark border-0 p-3 mb-3 rounded-4 shadow-sm">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="small text-secondary fw-bold ms-1 mb-1">BUSCA RÁPIDA</label>
                        <input type="text" name="busca" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3" placeholder="Código, cliente, tel..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-secondary fw-bold ms-1 mb-1">DATA INÍCIO</label>
                        <input type="date" name="data_inicio" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3" value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-secondary fw-bold ms-1 mb-1">DATA FIM</label>
                        <input type="date" name="data_fim" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3" value="<?= htmlspecialchars($_GET['data_fim'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-secondary fw-bold ms-1 mb-1">STATUS</label>
                        <select name="status" class="form-select bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3">
                            <option value="">Todos os Status</option>
                            <option value="Aberto" <?= ($_GET['status'] ?? '') === 'Aberto' ? 'selected' : '' ?>>Recebido / Aberto</option>
                            <option value="Em Andamento" <?= ($_GET['status'] ?? '') === 'Em Andamento' ? 'selected' : '' ?>>Em Preparo</option>
                            <option value="Saiu para entrega" <?= ($_GET['status'] ?? '') === 'Saiu para entrega' ? 'selected' : '' ?>>Saiu para Entrega</option>
                            <option value="Finalizado" <?= ($_GET['status'] ?? '') === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        </select>
                    </div>
                    <div class="col-md-2 mt-auto">
                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2"><i class="fas fa-filter me-1"></i> Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- KANBAN DE 4 FASES COM ROLAGEM VERTICAL INTERNA -->
            <div class="row g-3">

                <!-- 1. RECEBIDOS -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header text-primary">
                            <span><i class="fas fa-bell me-1"></i> 1. Recebidos</span>
                            <span class="badge bg-primary rounded-pill"><?= count($faseRecebido) ?></span>
                        </div>
                        <div class="kanban-cards-wrapper">
                            <?php if (empty($faseRecebido)): ?>
                                <p class="text-secondary small text-center mt-4">Nenhum pedido novo.</p>
                            <?php else: ?>
                                <?php foreach ($faseRecebido as $ped): ?>
                                    <?= renderCardPedidoPro($ped, $lojaNome, 'recebido') ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 2. EM PREPARO -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header text-warning">
                            <span><i class="fas fa-fire me-1"></i> 2. Em Preparo</span>
                            <span class="badge bg-warning text-dark rounded-pill"><?= count($faseAndamento) ?></span>
                        </div>
                        <div class="kanban-cards-wrapper">
                            <?php if (empty($faseAndamento)): ?>
                                <p class="text-secondary small text-center mt-4">Nenhum em preparo.</p>
                            <?php else: ?>
                                <?php foreach ($faseAndamento as $ped): ?>
                                    <?= renderCardPedidoPro($ped, $lojaNome, 'preparo') ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 3. SAIU PARA ENTREGA -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header" style="color: #c084fc;">
                            <span><i class="fas fa-motorcycle me-1"></i> 3. Em Rota</span>
                            <span class="badge bg-secondary rounded-pill"><?= count($faseEntrega) ?></span>
                        </div>
                        <div class="kanban-cards-wrapper">
                            <?php if (empty($faseEntrega)): ?>
                                <p class="text-secondary small text-center mt-4">Nenhum em rota.</p>
                            <?php else: ?>
                                <?php foreach ($faseEntrega as $ped): ?>
                                    <?= renderCardPedidoPro($ped, $lojaNome, 'entrega') ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 4. FINALIZADOS (DO DIA ATUAL + BOTÃO HISTÓRICO COMPLETO) -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header text-success">
                            <span><i class="fas fa-check-double me-1"></i> 4. Finalizados (Hoje)</span>
                            <span class="badge bg-success rounded-pill"><?= count($faseFinalizado) ?></span>
                        </div>
                        
                        <!-- BOTÃO QUE ABRE A TELA DE HISTÓRICO PASSADO -->
                        <button type="button" class="btn btn-outline-light btn-sm w-100 rounded-pill mb-2 fw-bold" onclick="abrirTelaHistorico()">
                            <i class="fas fa-history me-1"></i> Histórico Completo / PDF
                        </button>

                        <div class="kanban-cards-wrapper">
                            <?php if (empty($faseFinalizado)): ?>
                                <p class="text-secondary small text-center mt-4">Nenhum finalizado hoje.</p>
                            <?php else: ?>
                                <?php foreach ($faseFinalizado as $ped): ?>
                                    <?= renderCardPedidoPro($ped, $lojaNome, 'finalizado') ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>

<!-- =================================================================== -->
<!-- TELA DE APOIO / MODAL: HISTÓRICO COMPLETO DE PEDIDOS COM PDF & CUPOM -->
<!-- =================================================================== -->
<div class="modal fade no-print" id="modalHistoricoPedidos" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary px-4 py-3">
                <div>
                    <h5 class="fw-bold mb-0"><i class="fas fa-archive text-success me-2"></i>Histórico & Relatório Completo de Vendas</h5>
                    <small class="text-secondary">Consulte pedidos antigos, reimprima cupons e gere relatórios em PDF.</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success rounded-pill btn-sm px-3 fw-bold" onclick="exportarRelatorioPDF()">
                        <i class="fas fa-file-pdf me-1"></i> Gerar PDF / Imprimir Relatório
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-4">
                <!-- Filtros do Histórico -->
                <div class="card bg-black bg-opacity-40 border-0 p-3 mb-4 rounded-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="small text-secondary fw-bold">DE:</label>
                            <input type="date" id="hist_dt_ini" class="form-control bg-secondary bg-opacity-25 border-0 text-white rounded-pill px-3" value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-secondary fw-bold">ATÉ:</label>
                            <input type="date" id="hist_dt_fim" class="form-control bg-secondary bg-opacity-25 border-0 text-white rounded-pill px-3" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-secondary fw-bold">STATUS:</label>
                            <select id="hist_status" class="form-select bg-secondary bg-opacity-25 border-0 text-white rounded-pill px-3">
                                <option value="">Todos</option>
                                <option value="Finalizado">Apenas Finalizados</option>
                                <option value="Aberto">Recebidos</option>
                                <option value="Em Andamento">Em Andamento</option>
                                <option value="Cancelado">Cancelados</option>
                            </select>
                        </div>
                        <div class="col-md-3 mt-auto">
                            <button type="button" class="btn btn-success w-100 rounded-pill fw-bold" onclick="carregarHistoricoAjax()">
                                <i class="fas fa-search me-1"></i> Consultar Período
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Resumo dos Valores do Período -->
                <div class="row g-3 mb-4" id="cards-resumo-historico">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <span class="text-secondary small fw-bold">FATURAMENTO NO PERÍODO</span>
                            <h3 class="fw-bold text-success mb-0 mt-1" id="hist-total-faturado">R$ 0,00</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <span class="text-secondary small fw-bold">PEDIDOS CONCLUÍDOS</span>
                            <h3 class="fw-bold text-white mb-0 mt-1" id="hist-total-qtd">0 pedidos</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <span class="text-secondary small fw-bold">TICKET MÉDIO</span>
                            <h3 class="fw-bold text-info mb-0 mt-1" id="hist-ticket-medio">R$ 0,00</h3>
                        </div>
                    </div>
                </div>

                <!-- Tabela Completa do Histórico -->
                <div class="card card-custom p-3 shadow" id="area-tabela-historico">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead>
                                <tr class="text-secondary small">
                                    <th>Cód.</th>
                                    <th>Data / Hora</th>
                                    <th>Cliente</th>
                                    <th>WhatsApp</th>
                                    <th>Bairro</th>
                                    <th>Forma Pgto</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-corpo-historico">
                                <tr><td colspan="9" class="text-center py-4 text-secondary">Clique em "Consultar Período" para carregar os pedidos.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- MODAL GRANDE: DETALHES DO PEDIDO -->
<div class="modal fade no-print" id="modalDetalhesPedido" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0 rounded-4 text-white shadow-lg p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-3">
                <div>
                    <h4 class="fw-bold mb-0" id="det-codigo">Pedido #PED...</h4>
                    <span class="badge bg-primary" id="det-status-badge">Status</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill px-3 btn-sm" onclick="imprimirCupomAtual()">
                        <i class="fas fa-print me-1"></i> Imprimir Cupom Térmico (80mm)
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-5">
                    <div class="p-3 rounded-3 bg-secondary bg-opacity-10 mb-3">
                        <h6 class="fw-bold text-success mb-2"><i class="fas fa-user me-2"></i>Cliente</h6>
                        <p class="mb-1"><b id="det-nome">Nome do Cliente</b></p>
                        <p class="mb-0 text-secondary"><i class="fab fa-whatsapp me-1"></i> <span id="det-tel">WhatsApp</span></p>
                    </div>

                    <div class="p-3 rounded-3 bg-secondary bg-opacity-10 mb-3">
                        <h6 class="fw-bold text-success mb-2"><i class="fas fa-map-marker-alt me-2"></i>Endereço de Entrega</h6>
                        <p class="mb-1" id="det-endereco">Rua, Nº</p>
                        <p class="mb-0 text-secondary" id="det-bairro">Bairro - Cidade/UF</p>
                    </div>

                    <div class="p-3 rounded-3 bg-secondary bg-opacity-10">
                        <h6 class="fw-bold text-success mb-2"><i class="fas fa-credit-card me-2"></i>Pagamento</h6>
                        <p class="mb-1"><b>Forma:</b> <span id="det-pgto">Dinheiro</span></p>
                        <p class="mb-0 text-secondary"><b>Data/Hora:</b> <span id="det-data">00/00/0000 00:00</span></p>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="p-3 rounded-3 bg-secondary bg-opacity-10 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-success mb-3"><i class="fas fa-hamburger me-2"></i>Itens do Pedido</h6>
                            <div id="det-itens" class="p-2 bg-black bg-opacity-25 rounded-3 mb-3 small" style="max-height: 250px; overflow-y: auto;"></div>
                        </div>

                        <div class="border-top border-secondary pt-3">
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Subtotal:</span> <span id="det-subtotal">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Taxa de Entrega:</span> <span id="det-taxa">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold fs-5 text-white mt-2">
                                <span>TOTAL DO PEDIDO:</span> <span class="text-success" id="det-total">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ÁREA DE IMPRESSÃO CUPOM TÉRMICO TOMATO 80MM (PAPEL) -->
<div id="area-cupom-impressao" style="display:none;">
    <div class="cupom-termico">
        <div class="titulo-loja"><?= htmlspecialchars($lojaNome) ?></div>
        <div style="text-align: center; font-size: 11px;">
            <?= htmlspecialchars($lojaEndereco) ?><br>
            TEL/WHATS: <?= htmlspecialchars($lojaTelefone) ?>
        </div>

        <div class="divisor"></div>

        <div style="display:flex; justify-content:space-between;">
            <span>PEDIDO: <span id="cupom-codigo" class="destaque">#PED</span></span>
            <span id="cupom-status">ABERTO</span>
        </div>
        <div>CHEGADA: <span id="cupom-data">00/00 00:00</span></div>

        <div class="divisor"></div>

        <div>CLIENTE: <span id="cupom-cliente" class="destaque">NOME</span></div>
        <div>WHATSAPP: <span id="cupom-telefone">000000000</span></div>
        <div>END: <span id="cupom-endereco">RUA</span></div>
        <div>BAIRRO: <span id="cupom-bairro">BAIRRO</span></div>

        <div class="divisor"></div>
        <div style="text-align:center; margin-bottom: 3px;">*** ITENS DO PEDIDO ***</div>

        <div id="cupom-itens" class="itens-bloco"></div>

        <div class="divisor"></div>

        <div style="display:flex; justify-content:space-between;">
            <span>SUBTOTAL:</span>
            <span id="cupom-subtotal">R$ 0,00</span>
        </div>
        <div style="display:flex; justify-content:space-between;">
            <span>TAXA ENTREGA:</span>
            <span id="cupom-taxa">R$ 0,00</span>
        </div>
        <div class="divisor"></div>
        <div style="display:flex; justify-content:space-between;" class="destaque">
            <span>TOTAL:</span>
            <span id="cupom-total">R$ 0,00</span>
        </div>
        <div style="margin-top: 3px;">PAGAMENTO: <span id="cupom-pgto">DINHEIRO</span></div>

        <div class="divisor"></div>
        <div style="text-align:center; font-size: 11px; margin-top: 5px;">
            AGRADECEMOS A PREFERENCIA!<br>
            CARDAPIO DIGITAL PWA
        </div>
    </div>
</div>

<!-- ÁUDIO DO ALARME -->
<audio id="audioCampainha" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
</audio>

<?php
function renderCardPedidoPro($p, $nomeLoja, $fase) {
    $pJson = htmlspecialchars(json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
    $linkWhatsDireto = WhatsAppService::gerarLinkStatus($p, $nomeLoja);
    ob_start();
?>
    <div class="order-card" id="card-ped-<?= $p['id'] ?>">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-bold text-white small">#<?= htmlspecialchars($p['codigo_pedido']) ?></span>
            <span class="text-secondary" style="font-size: 11px;"><?= date('H:i', strtotime($p['data'])) ?></span>
        </div>

        <h6 class="fw-bold mb-1 text-light text-truncate"><?= htmlspecialchars($p['nome']) ?></h6>
        <p class="text-secondary small mb-2 text-truncate"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?= htmlspecialchars($p['bairro'] ?: 'Balcão') ?></p>

        <div class="p-2 rounded bg-black bg-opacity-25 small text-secondary mb-2" style="max-height: 65px; overflow-y: auto; font-size: 12px;">
            <?= $p['resumo_pedidos'] ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-secondary small">Total:</span>
            <span class="fw-bold text-success">R$ <?= number_format($p['total'], 2, ',', '.') ?></span>
        </div>

        <div class="d-flex flex-column gap-1">
            <button type="button" class="btn btn-outline-light btn-sm w-100 rounded-3 fw-bold py-1" onclick="abrirModalDetalhes(<?= $pJson ?>)">
                <i class="fas fa-eye me-1"></i> Ver Grande
            </button>

            <?php if ($fase === 'recebido'): ?>
                <button type="button" class="btn btn-warning btn-sm w-100 fw-bold text-dark rounded-3 py-1" 
                        onclick="avancarFaseENotificar(<?= $p['id'] ?>, 'Em Andamento', '<?= $p['telefone'] ?>', '<?= addslashes($p['nome']) ?>', '<?= addslashes($p['codigo_pedido']) ?>', '<?= addslashes($nomeLoja) ?>')">
                    <i class="fas fa-fire me-1"></i> Preparar & Zap
                </button>
            <?php elseif ($fase === 'preparo'): ?>
                <button type="button" class="btn btn-info btn-sm w-100 fw-bold text-white rounded-3 py-1" 
                        onclick="avancarFaseENotificar(<?= $p['id'] ?>, 'Saiu para entrega', '<?= $p['telefone'] ?>', '<?= addslashes($p['nome']) ?>', '<?= addslashes($p['codigo_pedido']) ?>', '<?= addslashes($nomeLoja) ?>')">
                    <i class="fas fa-motorcycle me-1"></i> Despachar & Zap
                </button>
            <?php elseif ($fase === 'entrega'): ?>
                <button type="button" class="btn btn-success btn-sm w-100 fw-bold rounded-3 py-1" 
                        onclick="avancarFaseENotificar(<?= $p['id'] ?>, 'Finalizado', '<?= $p['telefone'] ?>', '<?= addslashes($p['nome']) ?>', '<?= addslashes($p['codigo_pedido']) ?>', '<?= addslashes($nomeLoja) ?>')">
                    <i class="fas fa-check-double me-1"></i> Concluir & Zap
                </button>
            <?php endif; ?>

            <a href="<?= $linkWhatsDireto ?>" target="_blank" class="btn-whatsapp py-1">
                <i class="fab fa-whatsapp me-1"></i> WhatsApp
            </a>
        </div>
    </div>
<?php
    return ob_get_clean();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let pedidoAbertoGlobal = null;
let listaHistoricoCache = [];

function abrirModalDetalhes(p) {
    pedidoAbertoGlobal = p;

    document.getElementById('det-codigo').innerText = '#' + p.codigo_pedido;
    document.getElementById('det-status-badge').innerText = p.status;
    document.getElementById('det-nome').innerText = p.nome;
    document.getElementById('det-tel').innerText = p.telefone;
    document.getElementById('det-endereco').innerText = p.rua + (p.unidade ? ', Nº ' + p.unidade : '');
    document.getElementById('det-bairro').innerText = (p.bairro || 'Balcão') + (p.cidade ? ' - ' + p.cidade + '/' + p.uf : '');
    document.getElementById('det-pgto').innerText = p.forma_pagamento || 'Não informada';
    document.getElementById('det-data').innerText = p.data;
    document.getElementById('det-itens').innerHTML = p.resumo_pedidos;
    document.getElementById('det-subtotal').innerText = 'R$ ' + parseFloat(p.sub_total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    document.getElementById('det-taxa').innerText = 'R$ ' + parseFloat(p.valor_taxa || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    document.getElementById('det-total').innerText = 'R$ ' + parseFloat(p.total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});

    new bootstrap.Modal(document.getElementById('modalDetalhesPedido')).show();
}

function imprimirCupomAtual() {
    if (!pedidoAbertoGlobal) return;
    const p = pedidoAbertoGlobal;

    document.getElementById('cupom-codigo').innerText = '#' + p.codigo_pedido;
    document.getElementById('cupom-data').innerText = p.data;
    document.getElementById('cupom-status').innerText = p.status.toUpperCase();
    document.getElementById('cupom-cliente').innerText = p.nome;
    document.getElementById('cupom-telefone').innerText = p.telefone;
    document.getElementById('cupom-endereco').innerText = p.rua + (p.unidade ? ', Nº ' + p.unidade : '');
    document.getElementById('cupom-bairro').innerText = p.bairro || 'Balcão';
    document.getElementById('cupom-itens').innerHTML = p.resumo_pedidos.replace(/<br\s*[\/]?>/gi, "<br>");
    document.getElementById('cupom-subtotal').innerText = 'R$ ' + parseFloat(p.sub_total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    document.getElementById('cupom-taxa').innerText = 'R$ ' + parseFloat(p.valor_taxa || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    document.getElementById('cupom-total').innerText = 'R$ ' + parseFloat(p.total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    document.getElementById('cupom-pgto').innerText = p.forma_pagamento.toUpperCase();

    const areaCupom = document.getElementById('area-cupom-impressao');
    areaCupom.style.display = 'block';
    window.print();
    areaCupom.style.display = 'none';
}

function avancarFaseENotificar(pedidoId, novoStatus, telefone, cliente, codigo, nomeLoja) {
    const msg = `Olá *${cliente}*,\n\nInformamos que o Status do seu Pedido *#${codigo}* foi atualizado para *${novoStatus}*.\nAguarde mais novidades por aqui no *${nomeLoja}*.`;

    let telLimpo = telefone.replace(/\D/g, '');
    if (telLimpo.length === 10 || telLimpo.length === 11) telLimpo = '55' + telLimpo;
    const urlWhatsApp = `https://api.whatsapp.com/send?phone=${telLimpo}&text=` + encodeURIComponent(msg);

    const formData = new FormData();
    formData.append('acao_pedido', 'mudar_status');
    formData.append('pedido_id', pedidoId);
    formData.append('novo_status', novoStatus);

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        window.open(urlWhatsApp, '_blank');
        Swal.fire({
            icon: 'success',
            title: 'Fase Atualizada!',
            text: `Pedido #${codigo} atualizado para "${novoStatus}".`,
            background: '#1b2e4b',
            color: '#fff',
            timer: 1800,
            showConfirmButton: false
        }).then(() => {
            window.location.reload();
        });
    })
    .catch(() => {
        window.open(urlWhatsApp, '_blank');
        window.location.reload();
    });
}

// ===================================================================
// ROTINAS DA TELA DE HISTÓRICO PASSADO, PDF E REIMPRESSÃO
// ===================================================================
function abrirTelaHistorico() {
    new bootstrap.Modal(document.getElementById('modalHistoricoPedidos')).show();
    carregarHistoricoAjax();
}

function carregarHistoricoAjax() {
    const dtIni = document.getElementById('hist_dt_ini').value;
    const dtFim = document.getElementById('hist_dt_fim').value;
    const status = document.getElementById('hist_status').value;

    const url = window.location.pathname + `?ajax_historico=1&dt_ini=${dtIni}&dt_fim=${dtFim}&status_hist=${encodeURIComponent(status)}`;

    fetch(url)
    .then(r => r.json())
    .then(dados => {
        listaHistoricoCache = dados;
        renderTabelaHistorico(dados);
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Erro ao carregar histórico.' });
    });
}

function renderTabelaHistorico(pedidos) {
    const tbody = document.getElementById('tabela-corpo-historico');
    let totalFat = 0;
    let qtd = pedidos.length;

    if (pedidos.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4 text-secondary">Nenhum pedido encontrado para o período selecionado.</td></tr>`;
        document.getElementById('hist-total-faturado').innerText = 'R$ 0,00';
        document.getElementById('hist-total-qtd').innerText = '0 pedidos';
        document.getElementById('hist-ticket-medio').innerText = 'R$ 0,00';
        return;
    }

    let html = "";
    pedidos.forEach(p => {
        const valTotal = parseFloat(p.total || 0);
        totalFat += valTotal;

        const pJson = JSON.stringify(p).replace(/"/g, '&quot;');

        html += `<tr>
            <td class="fw-bold">#${p.codigo_pedido}</td>
            <td class="small text-secondary">${p.data}</td>
            <td><b>${p.nome}</b></td>
            <td>${p.telefone}</td>
            <td>${p.bairro || 'Balcão'}</td>
            <td>${p.forma_pagamento}</td>
            <td class="text-success fw-bold">R$ ${valTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
            <td><span class="badge bg-secondary">${p.status}</span></td>
            <td class="text-end">
                <button type="button" class="btn btn-warning btn-sm rounded-circle text-dark me-1" onclick="imprimirCupomHistorico(${pJson})" title="Imprimir Cupom 80mm">
                    <i class="fas fa-print"></i>
                </button>
                <button type="button" class="btn btn-info btn-sm rounded-circle text-white" onclick="abrirModalDetalhes(${pJson})" title="Ver Detalhes">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html;

    const ticketMedio = qtd > 0 ? (totalFat / qtd) : 0;
    document.getElementById('hist-total-faturado').innerText = 'R$ ' + totalFat.toLocaleString('pt-BR', {minimumFractionDigits: 2});
    document.getElementById('hist-total-qtd').innerText = qtd + ' pedidos';
    document.getElementById('hist-ticket-medio').innerText = 'R$ ' + ticketMedio.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}

function imprimirCupomHistorico(p) {
    pedidoAbertoGlobal = p;
    imprimirCupomAtual();
}

// GERA O RELATÓRIO FORMATADO EM PDF (JANELA DE IMPRESSÃO / SALVAR COMO PDF)
function exportarRelatorioPDF() {
    const dtIni = document.getElementById('hist_dt_ini').value;
    const dtFim = document.getElementById('hist_dt_fim').value;
    const fat = document.getElementById('hist-total-faturado').innerText;
    const qtd = document.getElementById('hist-total-qtd').innerText;
    const ticket = document.getElementById('hist-ticket-medio').innerText;

    let linhas = "";
    listaHistoricoCache.forEach(p => {
        linhas += `<tr>
            <td style="padding:6px; border:1px solid #ddd;">#${p.codigo_pedido}</td>
            <td style="padding:6px; border:1px solid #ddd;">${p.data}</td>
            <td style="padding:6px; border:1px solid #ddd;">${p.nome}</td>
            <td style="padding:6px; border:1px solid #ddd;">${p.bairro || 'Balcão'}</td>
            <td style="padding:6px; border:1px solid #ddd;">${p.forma_pagamento}</td>
            <td style="padding:6px; border:1px solid #ddd; text-align:right;">R$ ${parseFloat(p.total).toFixed(2)}</td>
            <td style="padding:6px; border:1px solid #ddd;">${p.status}</td>
        </tr>`;
    });

    const janela = window.open('', '', 'width=900,height=700');
    janela.document.write(`<html><head><title>Relatório de Pedidos - <?= addslashes($lojaNome) ?></title>
    <style>body{font-family:sans-serif; padding:20px; color:#333;} table{width:100%; border-collapse:collapse; margin-top:20px;} th{background:#f4f4f4; border:1px solid #ddd; padding:8px; text-align:left;} .resumo{margin-top:15px; padding:15px; background:#f9f9f9; border-radius:8px;}</style>
    </head><body>
    <h2>Relatório Gerencial de Vendas - <?= addslashes($lojaNome) ?></h2>
    <p>Período: ${dtIni} até ${dtFim}</p>
    <div class="resumo">
        <b>Total Faturado:</b> ${fat} | <b>Total de Pedidos:</b> ${qtd} | <b>Ticket Médio:</b> ${ticket}
    </div>
    <table>
        <thead><tr><th>Pedido</th><th>Data/Hora</th><th>Cliente</th><th>Bairro</th><th>Pagamento</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>${linhas}</tbody>
    </table>
    <script>window.onload = function(){ window.print(); window.close(); }<\/script>
    </body></html>`);
    janela.document.close();
}

// POLLING CONTINUO DE NOVOS PEDIDOS (ALARME SONORO)
setInterval(function() {
    fetch('/<?= $loja['user_virtual_env'] ?>?acao_loja=checar_novos_pedidos')
    .then(r => r.json())
    .then(data => {
        if (data.tem_novos && data.pedidos.length > 0) {
            const audio = document.getElementById('audioCampainha');
            if (audio) audio.play().catch(e => {});

            const p = data.pedidos[0];
            Swal.fire({
                icon: 'warning',
                title: '🔔 NOVO PEDIDO RECEBIDO!',
                html: `<b>Pedido:</b> #${p.codigo_pedido}<br><b>Cliente:</b> ${p.nome}<br><b>Total:</b> R$ ${parseFloat(p.total).toFixed(2)}`,
                background: '#1b2e4b',
                color: '#fff',
                confirmButtonColor: '#34af23',
                confirmButtonText: 'Ver Pedido no Kanban'
            }).then(() => {
                window.location.reload();
            });
        }
    })
    .catch(e => {});
}, 4000);
</script>
</body>
</html>