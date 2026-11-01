<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Horários de Atendimento | <?= htmlspecialchars($loja['user_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0e1726; font-family: 'Plus Jakarta Sans', sans-serif; color: #fff; }
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }
        .card-custom { background: #1b2e4b; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-4 text-center"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($loja['user_name']) ?></h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin"><i class="fas fa-columns me-2"></i> Pedidos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?pagina=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?pagina=categorias"><i class="fas fa-tags me-2"></i> Categorias</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?pagina=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?pagina=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                </nav>
            </div>
            <div>
                <a href="/<?= $loja['user_virtual_env'] ?>/admin?acao=logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">Sair</a>
            </div>
        </aside>

        <main class="col-md-10 p-4">
            <div class="card card-custom p-4" style="max-width: 800px;">
                <h4 class="fw-bold mb-1">Horários de Funcionamento</h4>
                <p class="text-secondary small mb-4">Configure os dias e horários em que a sua lanchonete aceita pedidos automáticos.</p>

                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success border-0 rounded-4 py-2 small mb-4">
                        <i class="fas fa-check-circle me-1"></i> Horários atualizados com sucesso!
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <?php
                    $diasSemana = [
                        'segunda' => 'Segunda-feira',
                        'terca'   => 'Terça-feira',
                        'quarta'  => 'Quarta-feira',
                        'quinta'  => 'Quinta-feira',
                        'sexta'   => 'Sexta-feira',
                        'sabado'  => 'Sábado',
                        'domingo' => 'Domingo'
                    ];

                    foreach ($diasSemana as $chave => $rotulo):
                        $configStr = $empresa["config_{$chave}"] ?? 'on 18:00-23:59';
                        $partes = explode(' ', $configStr);
                        $estaAberto = ($partes[0] ?? 'on') === 'on';
                        $horas = explode('-', $partes[1] ?? '18:00-23:59');
                        $inicio = $horas[0] ?? '18:00';
                        $fim    = $horas[1] ?? '23:59';
                    ?>
                        <div class="row g-2 align-items-center mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status_<?= $chave ?>" id="check_<?= $chave ?>" <?= $estaAberto ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold small" for="check_<?= $chave ?>"><?= $rotulo ?></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-secondary bg-opacity-25 border-0 text-white">Abertura</span>
                                    <input type="time" name="inicio_<?= $chave ?>" class="form-control bg-secondary bg-opacity-10 border-0 text-white" value="<?= $inicio ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-secondary bg-opacity-25 border-0 text-white">Fechamento</span>
                                    <input type="time" name="fim_<?= $chave ?>" class="form-control bg-secondary bg-opacity-10 border-0 text-white" value="<?= $fim ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-success rounded-pill fw-bold px-5 py-2 mt-3">Salvar Horários</button>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>