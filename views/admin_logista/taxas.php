<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Taxas de Entrega | <?= htmlspecialchars($loja['user_name']) ?></title>
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
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?pagina=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?pagina=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                </nav>
            </div>
            <div>
                <a href="/<?= $loja['user_virtual_env'] ?>/admin?acao=logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">Sair</a>
            </div>
        </aside>

        <main class="col-md-10 p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold mb-3">Cadastrar Bairro / Região</h5>
                        <form method="POST">
                            <input type="hidden" name="acao" value="salvar">
                            <div class="mb-3">
                                <label class="small fw-bold">NOME DO BAIRRO</label>
                                <input type="text" name="bairro" class="form-control bg-secondary bg-opacity-25 border-0 text-white" placeholder="Ex: Centro, Vila Nova..." required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <label class="small fw-bold">CIDADE</label>
                                    <input type="text" name="cidade" class="form-control bg-secondary bg-opacity-25 border-0 text-white" value="Caçapava" required>
                                </div>
                                <div class="col-4">
                                    <label class="small fw-bold">UF</label>
                                    <input type="text" name="uf" class="form-control bg-secondary bg-opacity-25 border-0 text-white" value="SP" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">TAXA DE ENTREGA (R$)</label>
                                <input type="text" name="taxa" class="form-control bg-secondary bg-opacity-25 border-0 text-white" placeholder="5,00" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Salvar Bairro</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold mb-3">Bairros de Entrega</h5>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Bairro</th>
                                        <th>Cidade / UF</th>
                                        <th>Taxa</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bairros as $b): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($b['bairro']) ?></td>
                                            <td><?= htmlspecialchars($b['cidade']) ?> / <?= htmlspecialchars($b['uf']) ?></td>
                                            <td class="text-success fw-bold">R$ <?= number_format($b['taxa'], 2, ',', '.') ?></td>
                                            <td class="text-end">
                                                <form method="POST" onsubmit="return confirm('Remover este bairro?');" style="display:inline;">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-circle"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>