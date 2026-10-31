<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Digital PWA - Admin Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; }
        .sidebar { min-width: 250px; height: 100vh; background: #1A4D2E; color: white; position: fixed; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); border-radius: 10px; margin: 5px 15px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #FFC107; color: #1A4D2E; }
        .main-content { margin-left: 250px; padding: 30px; width: 100%; }
        .card-stat { border: none; border-radius: 15px; border-left: 5px solid #FFC107; }
    </style>
</head>
<body class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3">
        <img src="/assets/img/logo_bdsoftech.png" width="120" class="mx-auto mb-4 bg-white p-2 rounded">
        <h6 class="text-uppercase small fw-bold opacity-50 px-3">Menu Principal</h6>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="#" class="nav-link"><i class="bi bi-shop me-2"></i> Gerenciar Lojas</a></li>
            <li><a href="#" class="nav-link"><i class="bi bi-people me-2"></i> Clientes Master</a></li>
            <li><a href="#" class="nav-link"><i class="bi bi-gear me-2"></i> Configurações</a></li>
        </ul>
        <hr>
        <a href="/" class="btn btn-danger btn-sm w-100 rounded-pill">Sair do Sistema</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Visão Geral do <span class="fw-bold">Digital PWA</span></h2>
            <button class="btn btn-success rounded-pill"><i class="bi bi-plus-circle me-2"></i> Nova Unidade</button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-stat shadow-sm p-3">
                    <span class="text-muted small">Total Faturado</span>
                    <h3 class="fw-bold">R$ 15.420,00</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat shadow-sm p-3">
                    <span class="text-muted small">Lojas Ativas</span>
                    <h3 class="fw-bold">12 Unidades</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat shadow-sm p-3">
                    <span class="text-muted small">Pedidos Hoje</span>
                    <h3 class="fw-bold">452</h3>
                </div>
            </div>
        </div>

        <!-- Tabela de Clientes -->
        <div class="card border-none shadow-sm rounded-4 p-4">
            <h5 class="mb-3">Lanchonetes Registradas</h5>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Lanchonete</th>
                        <th>URL do Cardápio</th>
                        <th>Status</th>
                        <th>Vencimento</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded p-2 me-2"><i class="bi bi-burger"></i></div>
                                <div><strong>Hugo Lanches</strong><br><small class="text-muted">CNPJ: 00.000.000/0001-00</small></div>
                            </div>
                        </td>
                        <td><a href="/hugo-lanches" target="_blank" class="text-decoration-none">/hugo-lanches <i class="bi bi-box-arrow-up-right small"></i></a></td>
                        <td><span class="badge bg-success rounded-pill">Ativo</span></td>
                        <td>12/10/2026</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>