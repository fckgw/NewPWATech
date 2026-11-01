<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel de Pedidos | <?= htmlspecialchars($loja['user_name']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-body: #0e1726;
            --bg-card: #1b2e4b;
            --pwa-green: #34af23;
        }
        body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif; color: #fff; min-height: 100vh; }
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; border-right: 1px solid rgba(255,255,255,0.05); }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }

        .stat-card { background: var(--bg-card); border-radius: 20px; padding: 22px; border: 1px solid rgba(255,255,255,0.05); }

        /* Kanban */
        .kanban-col { background: rgba(27, 46, 75, 0.4); border-radius: 20px; padding: 18px; min-height: 75vh; border: 1px solid rgba(255,255,255,0.05); }
        .kanban-header { font-weight: 800; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .order-card { background: var(--bg-card); border-radius: 16px; padding: 16px; margin-bottom: 14px; border: 1px solid rgba(255,255,255,0.08); transition: 0.2s; }
        .order-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-color: var(--pwa-green); }

        .badge-fase { font-size: 0.75rem; padding: 6px 12px; border-radius: 50px; font-weight: 700; }
        .fase-recebido { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .fase-preparo  { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .fase-entrega  { background: rgba(168, 85, 247, 0.2); color: #c084fc; }
        .fase-finalizado { background: rgba(52, 175, 35, 0.2); color: #4ade80; }

        .btn-whatsapp { background: #25D366; color: #fff; font-weight: 700; border-radius: 10px; font-size: 0.8rem; padding: 8px 12px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-whatsapp:hover { background: #1eb956; color: #fff; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR DO LOJISTA -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($loja['user_name']) ?></h5>
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
                    <i class="fas fa-external-link-alt me-1"></i> Abrir Meu Cardápio
                </a>
                <a href="/<?= $loja['user_virtual_env'] ?>/admin?acao=logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">
                    <i class="fas fa-power-off me-1"></i> Sair do Painel
                </a>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL -->
        <main class="col-md-10 p-4">

            <!-- STATS SUPERIORES -->
            <div class="row g-3 mb-4">
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

            <!-- FILTRO DE BUSCA -->
            <div class="card bg-dark border-0 p-3 mb-4 rounded-4">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="busca" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3" placeholder="Buscar por código, cliente ou WhatsApp..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="data" class="form-control bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3" value="<?= htmlspecialchars($_GET['data'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select bg-secondary bg-opacity-10 border-0 text-white rounded-pill px-3">
                            <option value="">Todos os Status</option>
                            <option value="Aberto">Recebido / Aberto</option>
                            <option value="Em Andamento">Em Preparo</option>
                            <option value="Saiu para entrega">Saiu para Entrega</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold"><i class="fas fa-filter me-1"></i> Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- KANBAN DE 4 FASES -->
            <div class="row g-3">

                <!-- FASE 1: RECEBIDO / ABERTO -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header text-primary">
                            <span><i class="fas fa-bell me-1"></i> 1. Recebidos</span>
                            <span class="badge bg-primary rounded-pill"><?= count($faseRecebido) ?></span>
                        </div>
                        <?php foreach ($faseRecebido as $ped): ?>
                            <?= renderCardPedido($ped, $loja['user_name'], 'recebido') ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FASE 2: EM PREPARO -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header text-warning">
                            <span><i class="fas fa-fire me-1"></i> 2. Em Preparo</span>
                            <span class="badge bg-warning text-dark rounded-pill"><?= count($faseAndamento) ?></span>
                        </div>
                        <?php foreach ($faseAndamento as $ped): ?>
                            <?= renderCardPedido($ped, $loja['user_name'], 'preparo') ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FASE 3: SAIU PARA ENTREGA -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header" style="color: #c084fc;">
                            <span><i class="fas fa-motorcycle me-1"></i> 3. Em Rota</span>
                            <span class="badge bg-secondary rounded-pill"><?= count($faseEntrega) ?></span>
                        </div>
                        <?php foreach ($faseEntrega as $ped): ?>
                            <?= renderCardPedido($ped, $loja['user_name'], 'entrega') ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FASE 4: FINALIZADOS -->
                <div class="col-md-3">
                    <div class="kanban-col">
                        <div class="kanban-header text-success">
                            <span><i class="fas fa-check-double me-1"></i> 4. Finalizados</span>
                            <span class="badge bg-success rounded-pill"><?= count($faseFinalizado) ?></span>
                        </div>
                        <?php foreach ($faseFinalizado as $ped): ?>
                            <?= renderCardPedido($ped, $loja['user_name'], 'finalizado') ?>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>

<?php
// Função Helper que renderiza o card de pedido no Kanban
function renderCardPedido($p, $nomeLoja, $fase) {
    $linkWhats = WhatsAppService::gerarLinkStatus($p, $nomeLoja);
    ob_start();
?>
    <div class="order-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-white">#<?= htmlspecialchars($p['codigo_pedido']) ?></span>
            <span class="text-secondary small"><?= date('H:i', strtotime($p['data'])) ?></span>
        </div>

        <h6 class="fw-bold mb-1 text-light"><?= htmlspecialchars($p['nome']) ?></h6>
        <p class="text-secondary small mb-2"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?= htmlspecialchars($p['bairro'] ?: 'Balcão') ?></p>

        <div class="p-2 rounded bg-black bg-opacity-25 small text-secondary mb-3" style="max-height: 80px; overflow-y: auto;">
            <?= $p['resumo_pedidos'] ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-secondary small">Total:</span>
            <span class="fw-bold text-success fs-6">R$ <?= number_format($p['total'], 2, ',', '.') ?></span>
        </div>

        <!-- AÇÕES DE TRANSIÇÃO DE STATUS -->
        <form method="POST" class="d-flex flex-column gap-2">
            <input type="hidden" name="acao_pedido" value="mudar_status">
            <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">

            <div class="d-flex gap-1">
                <?php if ($fase === 'recebido'): ?>
                    <button type="submit" name="novo_status" value="Em Andamento" class="btn btn-warning btn-sm w-100 fw-bold">
                        <i class="fas fa-fire me-1"></i> Preparar
                    </button>
                <?php elseif ($fase === 'preparo'): ?>
                    <button type="submit" name="novo_status" value="Saiu para entrega" class="btn btn-info btn-sm w-100 fw-bold text-white">
                        <i class="fas fa-motorcycle me-1"></i> Despachar
                    </button>
                <?php elseif ($fase === 'entrega'): ?>
                    <button type="submit" name="novo_status" value="Finalizado" class="btn btn-success btn-sm w-100 fw-bold">
                        <i class="fas fa-check me-1"></i> Concluir
                    </button>
                <?php endif; ?>
            </div>

            <!-- BOTÃO DIRETO DO WHATSAPP -->
            <a href="<?= $linkWhats ?>" target="_blank" class="btn-whatsapp">
                <i class="fab fa-whatsapp me-1"></i> Avisar no WhatsApp
            </a>
        </form>
    </div>
<?php
    return ob_get_clean();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>