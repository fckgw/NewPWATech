<?php
$lojaId   = $_SESSION['lojista_id'] ?? 0;
$lojaNome = $_SESSION['lojista_nome'] ?? 'Minha Loja';
$lojaSlug = $_SESSION['lojista_slug'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | <?= htmlspecialchars($lojaNome) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .sidebar { min-height: 100vh; background: #0e1726; color: #fff; padding: 25px; }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 8px; padding: 12px 16px; font-weight: 600; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }
        .card-stat { border-radius: 20px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.04); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar do Lojista -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <h4 class="fw-bold mb-4"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($lojaNome) ?></h4>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="#"><i class="fas fa-chart-line me-2"></i> Visão Geral</a>
                    <a class="nav-link" href="#"><i class="fas fa-hamburger me-2"></i> Produtos</a>
                    <a class="nav-link" href="#"><i class="fas fa-tags me-2"></i> Categorias</a>
                    <a class="nav-link" href="#"><i class="fas fa-motorcycle me-2"></i> Taxas de Entrega</a>
                    <a class="nav-link" href="#"><i class="fas fa-clock me-2"></i> Horários</a>
                </nav>
            </div>
            <div>
                <!-- Link direto para o Cardápio Público do Lojista -->
                <a href="/<?= htmlspecialchars($lojaSlug) ?>" target="_blank" class="btn btn-outline-success w-100 rounded-pill mb-2 fw-bold btn-sm">
                    <i class="fas fa-eye me-1"></i> Ver Cardápio
                </a>
                <a href="/<?= htmlspecialchars($lojaSlug) ?>/admin?acao=logout" class="btn btn-danger w-100 rounded-pill fw-bold btn-sm">
                    <i class="fas fa-power-off me-1"></i> Sair
                </a>
            </div>
        </aside>

        <!-- Conteúdo Principal do Dashboard -->
        <main class="col-md-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Painel de Controle</h3>
                    <p class="text-muted small">Gerencie as operações em tempo real da sua lanchonete.</p>
                </div>
                <div class="badge bg-success bg-opacity-10 text-success p-3 rounded-4 fw-bold">
                    Link Público: <code>pwa.bdsoft.com.br/<?= htmlspecialchars($lojaSlug) ?></code>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card card-stat p-4">
                        <span class="text-muted small fw-bold">STATUS DA LOJA</span>
                        <h4 class="fw-bold text-success mt-2 mb-0"><i class="fas fa-circle fa-xs me-2"></i> Aberto para Pedidos</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat p-4">
                        <span class="text-muted small fw-bold">PEDIDOS HOJE</span>
                        <h4 class="fw-bold text-dark mt-2 mb-0">0 pedidos</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stat p-4">
                        <span class="text-muted small fw-bold">TOTAL VENDIDO HOJE</span>
                        <h4 class="fw-bold text-dark mt-2 mb-0">R$ 0,00</h4>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>