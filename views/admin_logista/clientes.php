<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Clientes (CRM) | <?= htmlspecialchars($loja['user_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0e1726; font-family: 'Plus Jakarta Sans', sans-serif; color: #fff; min-height: 100vh; }
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }
        .card-custom { background: #1b2e4b; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .stat-badge { background: rgba(255,255,255,0.04); border-radius: 16px; padding: 18px; border: 1px solid rgba(255,255,255,0.06); text-decoration: none; color: #fff; display: block; transition: 0.2s; }
        .stat-badge:hover { transform: translateY(-3px); background: rgba(255,255,255,0.08); color: #fff; }
        .stat-badge.active { border-color: #34af23; background: rgba(52, 175, 35, 0.1); }
        .btn-whats { background: #25D366; color: #fff; font-weight: 700; border-radius: 50px; font-size: 0.8rem; padding: 6px 14px; text-decoration: none; }
        .btn-whats:hover { background: #1eb956; color: #fff; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-4 text-center"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($loja['user_name']) ?></h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin"><i class="fas fa-columns me-2"></i> Pedidos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=categorias"><i class="fas fa-tags me-2"></i> Categorias</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes"><i class="fas fa-users me-2"></i> Meus Clientes (CRM)</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=configuracoes"><i class="fas fa-cog me-2"></i> Configurações</a>
                </nav>
            </div>
            <div>
                <a href="/<?= $loja['user_virtual_env'] ?>" target="_blank" class="btn btn-outline-success w-100 rounded-pill mb-2 fw-bold btn-sm">Ver Cardápio</a>
                <a href="/<?= $loja['user_virtual_env'] ?>/admin?acao=logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">Sair</a>
            </div>
        </aside>

        <main class="col-md-10 p-4">
            <h3 class="fw-bold mb-1">CRM & Fidelização de Clientes</h3>
            <p class="text-secondary small mb-4">Acompanhe seus clientes mais fiéis, resgate os inativos e parabenize aniversariantes.</p>

            <!-- FILTROS INTELIGENTES DE CRM -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <a href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes&filtro=todos" class="stat-badge <?= $filtro === 'todos' ? 'active' : '' ?>">
                        <span class="text-secondary small fw-bold">TOTAL CADASTRADOS</span>
                        <h4 class="fw-bold text-white mt-1 mb-0"><?= $totalClientes ?> clientes</h4>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes&filtro=aniversariantes_dia" class="stat-badge <?= $filtro === 'aniversariantes_dia' ? 'active' : '' ?>">
                        <span class="text-warning small fw-bold"><i class="fas fa-birthday-cake me-1"></i> ANIVERSÁRIO HOJE</span>
                        <h4 class="fw-bold text-warning mt-1 mb-0"><?= $aniversariantesDia ?> clientes</h4>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes&filtro=aniversariantes_semana" class="stat-badge <?= $filtro === 'aniversariantes_semana' ? 'active' : '' ?>">
                        <span class="text-info small fw-bold"><i class="fas fa-gift me-1"></i> ANIVERSÁRIO NA SEMANA</span>
                        <h4 class="fw-bold text-info mt-1 mb-0"><?= $aniversariantesSemana ?> clientes</h4>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes&filtro=inativos_30" class="stat-badge <?= $filtro === 'inativos_30' ? 'active' : '' ?>">
                        <span class="text-danger small fw-bold"><i class="fas fa-user-clock me-1"></i> SEM PEDIR HÁ +30 DIAS</span>
                        <h4 class="fw-bold text-danger mt-1 mb-0"><?= $inativos30Dias ?> clientes</h4>
                    </a>
                </div>
            </div>

            <!-- TABELA DE CLIENTES -->
            <div class="card card-custom p-4">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>WhatsApp</th>
                                <th>Nascimento</th>
                                <th>Total de Pedidos</th>
                                <th>Total Gasto</th>
                                <th>Última Visita</th>
                                <th class="text-end">Ação WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clientes)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-secondary">Nenhum cliente encontrado neste filtro.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clientes as $c): 
                                    $telLimpo = preg_replace('/\D/', '', $c['telefone']);
                                    if (strlen($telLimpo) === 10 || strlen($telLimpo) === 11) $telLimpo = '55' . $telLimpo;
                                    
                                    // Mensagem personalizada para disparo rápido
                                    if ($filtro === 'aniversariantes_dia' || $filtro === 'aniversariantes_semana') {
                                        $msg = "Olá {$c['nome_completo']}! 🎂 Parabéns pelo seu aniversário! O {$loja['user_name']} tem um presente especial para você hoje no seu próximo pedido!";
                                    } elseif ($filtro === 'inativos_30') {
                                        $msg = "Olá {$c['nome_completo']}! Sentimos sua falta no {$loja['user_name']}! 🍔 Que tal pedir seu lanche favorito hoje com entrega grátis?";
                                    } else {
                                        $msg = "Olá {$c['nome_completo']}! Confira as novidades do cardápio no {$loja['user_name']}: pwa.bdsoft.com.br/{$loja['user_virtual_env']}";
                                    }
                                    $linkMsg = "https://api.whatsapp.com/send?phone={$telLimpo}&text=" . urlencode($msg);
                                ?>
                                    <tr>
                                        <td>
                                            <b class="text-white"><?= htmlspecialchars($c['nome_completo']) ?></b><br>
                                            <small class="text-secondary"><?= htmlspecialchars($c['email'] ?? 'Sem e-mail') ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($c['telefone']) ?></td>
                                        <td><?= $c['data_nascimento'] ? date('d/m/Y', strtotime($c['data_nascimento'])) : '-' ?></td>
                                        <td><span class="badge bg-secondary"><?= $c['total_pedidos'] ?> pedidos</span></td>
                                        <td class="text-success fw-bold">R$ <?= number_format((float)$c['total_gasto'], 2, ',', '.') ?></td>
                                        <td class="small text-secondary"><?= $c['ultima_visita'] ? date('d/m/Y H:i', strtotime($c['ultima_visita'])) : 'Nunca' ?></td>
                                        <td class="text-end">
                                            <a href="<?= $linkMsg ?>" target="_blank" class="btn-whats">
                                                <i class="fab fa-whatsapp me-1"></i> Chamar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>