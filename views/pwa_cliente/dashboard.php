<?php
/**
 * DIGITAL PWA - BDSoftech
 * Painel Administrativo do Lojista (Versão Modern Light)
 */

// Proteção básica: Verificar se o usuário está logado e pertence a esta loja
// No seu index.php, você deve garantir que $_SESSION['tenant_id'] == $loja['id']

$db = Conexao::getConexao();
$tenant_id = $loja['id'];

// Consultas para o Dashboard
$hoje = date('Y-m-d');
$stats_pedidos = $db->query("SELECT COUNT(*) as total FROM pedidos WHERE tenant_id = $tenant_id AND DATE(data_criacao) = '$hoje'")->fetch();
$stats_faturamento = $db->query("SELECT SUM(total) as total FROM pedidos WHERE tenant_id = $tenant_id AND status = 'Entregue' AND DATE(data_criacao) = '$hoje'")->fetch();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - <?= $loja['nome_fantasia'] ?></title>
    
    <!-- Bootstrap 5 e Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --admin-green: #1A4D2E; /* BDSoftech Green */
            --admin-yellow: #F5C400;
            --bg-body: #F4F7F6;
            --sidebar-width: 260px;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); overflow-x: hidden; }

        /* Sidebar Customizada */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            border-right: 1px solid #E9ECEF;
            z-index: 1000;
            padding: 20px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #636E72;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: 0.2s;
        }

        .nav-link i { font-size: 1.2rem; margin-right: 12px; }
        .nav-link:hover, .nav-link.active {
            background-color: var(--admin-green);
            color: white;
        }

        /* Conteúdo Principal */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        /* Cards de Estatísticas */
        .card-stat {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: 0.3s;
        }
        .card-stat:hover { transform: translateY(-5px); }
        .icon-shape {
            width: 50px; height: 50px;
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        /* Tabelas Modernas */
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        .status-pill {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-novo { background: #E3F2FD; color: #1976D2; }
        .status-preparo { background: #FFF3E0; color: #F57C00; }
        .status-finalizado { background: #E8F5E9; color: #388E3C; }

        /* Botão de PDV/Venda Rápida */
        .btn-pdv {
            background: var(--admin-yellow);
            color: #000;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="mb-5 text-center">
            <img src="/assets/img/logo_bdsoftech.png" width="100" class="mb-3">
            <h6 class="fw-bold mb-0 text-dark"><?= $loja['nome_fantasia'] ?></h6>
            <small class="text-muted">Painel Administrativo</small>
        </div>

        <nav>
            <a href="#" class="nav-link active"><i class="bi bi-house-door"></i> Início</a>
            <a href="#" class="nav-link"><i class="bi bi-receipt"></i> Pedidos <span class="badge bg-danger ms-auto rounded-pill">5</span></a>
            <a href="#" class="nav-link"><i class="bi bi-journal-text"></i> Cardápio</a>
            <a href="#" class="nav-link"><i class="bi bi-box-seam"></i> Ingredientes</a>
            <a href="#" class="nav-link"><i class="bi bi-people"></i> Clientes</a>
            <a href="#" class="nav-link"><i class="bi bi-bar-chart"></i> Relatórios</a>
            <hr>
            <a href="#" class="nav-link"><i class="bi bi-gear"></i> Configurações</a>
            <a href="/logout" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Sair</a>
        </nav>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        
        <!-- TOPBAR -->
        <header class="topbar">
            <div>
                <h3 class="fw-bold mb-0">Olá, Hugo! 👋</h3>
                <p class="text-muted">Veja como estão as vendas da sua lanchonete hoje.</p>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <div class="form-check form-switch me-3">
                    <input class="form-check-input" type="checkbox" id="switchLoja" checked>
                    <label class="form-check-label fw-bold text-success" for="switchLoja">Loja Aberta</label>
                </div>
                <button class="btn-pdv"><i class="bi bi-plus-lg me-2"></i> Novo Pedido</button>
            </div>
        </header>

        <!-- CARDS DE ESTATÍSTICAS -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card-stat">
                    <div class="icon-shape bg-primary text-white"><i class="bi bi-cart-check"></i></div>
                    <span class="text-muted small">Pedidos Hoje</span>
                    <h2 class="fw-bold mb-0"><?= $stats_pedidos['total'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stat">
                    <div class="icon-shape bg-success text-white"><i class="bi bi-currency-dollar"></i></div>
                    <span class="text-muted small">Faturamento (R$)</span>
                    <h2 class="fw-bold mb-0"><?= number_format($stats_faturamento['total'] ?? 0, 2, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stat">
                    <div class="icon-shape bg-warning text-dark"><i class="bi bi-clock-history"></i></div>
                    <span class="text-muted small">Ticket Médio</span>
                    <h2 class="fw-bold mb-0">R$ 42,50</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stat">
                    <div class="icon-shape bg-info text-white"><i class="bi bi-people"></i></div>
                    <span class="text-muted small">Novos Clientes</span>
                    <h2 class="fw-bold mb-0">12</h2>
                </div>
            </div>
        </div>

        <!-- ÚLTIMOS PEDIDOS -->
        <div class="row">
            <div class="col-lg-8">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Últimos Pedidos</h5>
                        <a href="#" class="btn btn-link text-decoration-none p-0">Ver todos</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="fw-bold">#1042</span></td>
                                    <td>João da Silva</td>
                                    <td>R$ 85,50</td>
                                    <td><span class="status-pill status-novo">Novo</span></td>
                                    <td><button class="btn btn-sm btn-outline-dark rounded-pill px-3">Detalhes</button></td>
                                </tr>
                                <tr>
                                    <td><span class="fw-bold">#1041</span></td>
                                    <td>Maria Oliveira</td>
                                    <td>R$ 42,00</td>
                                    <td><span class="status-pill status-preparo">Em Preparo</span></td>
                                    <td><button class="btn btn-sm btn-outline-dark rounded-pill px-3">Detalhes</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PRODUTOS MAIS VENDIDOS -->
            <div class="col-lg-4">
                <div class="table-container">
                    <h5 class="fw-bold mb-4">Mais Vendidos</h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://via.placeholder.com/50" class="rounded-3 me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold">X-Burger Especial</h6>
                            <small class="text-muted">45 vendas hoje</small>
                        </div>
                        <span class="fw-bold text-success">+ R$ 1.200</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://via.placeholder.com/50" class="rounded-3 me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold">Porção de Batata</h6>
                            <small class="text-muted">32 vendas hoje</small>
                        </div>
                        <span class="fw-bold text-success">+ R$ 640</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>