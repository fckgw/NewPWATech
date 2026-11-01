<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meus Produtos | <?= htmlspecialchars($loja['user_name']) ?></title>

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
        body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif; color: #fff; min-height: 100vh; }
        
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; border-right: 1px solid rgba(255,255,255,0.05); }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }

        .card-custom { background: var(--bg-card); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .form-control, .form-select { background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 12px; padding: 12px 15px; }
        .form-control:focus, .form-select:focus { background-color: rgba(255,255,255,0.08); border-color: #34af23; color: #fff; box-shadow: none; }
        
        .prod-thumb { width: 55px; height: 55px; border-radius: 14px; object-fit: cover; }
        .prod-thumb-fallback {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888ea8;
            font-size: 1.3rem;
            border: 1px dashed rgba(255,255,255,0.15);
        }
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR COMPLETA -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($loja['user_name']) ?></h5>
                    <span class="badge bg-success bg-opacity-20 text-success small mt-1">Loja Aberta</span>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin"><i class="fas fa-columns me-2"></i> Pedidos (Kanban)</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Catálogo de Produtos</h3>
                    <p class="text-secondary small">Cadastre novos lanches, altere preços e pause itens esgotados.</p>
                </div>
                <button class="btn btn-success rounded-pill fw-bold px-4 py-2" onclick="abrirModalProduto()">
                    <i class="fas fa-plus me-1"></i> Cadastrar Produto
                </button>
            </div>

            <div class="card card-custom p-4 shadow">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Foto</th>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Preço</th>
                                <th>Disponibilidade</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-produtos">
                            <?php if (empty($produtos)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-secondary">Nenhum produto cadastrado até o momento.</td></tr>
                            <?php else: ?>
                                <?php foreach ($produtos as $p): 
                                    $temImgFisica = false;
                                    $imgUrl = '';
                                    if (!empty($p['img_item']) && $p['img_item'] !== 'false') {
                                        $caminhoFisico = __DIR__ . '/../../uploads/' . $p['img_item'];
                                        if (file_exists($caminhoFisico)) {
                                            $temImgFisica = true;
                                            $imgUrl = '/uploads/' . htmlspecialchars($p['img_item']);
                                        }
                                    }
                                ?>
                                    <tr id="linha-prod-<?= $p['id'] ?>">
                                        <td>
                                            <?php if ($temImgFisica): ?>
                                                <img src="<?= $imgUrl ?>" class="prod-thumb shadow-sm" alt="Foto"
                                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'prod-thumb-fallback\'><i class=\'fas fa-hamburger\'></i></div>';">
                                            <?php else: ?>
                                                <div class="prod-thumb-fallback">
                                                    <i class="fas fa-hamburger"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <b class="text-white"><?= htmlspecialchars($p['nome_item']) ?></b><br>
                                            <small class="text-secondary"><?= htmlspecialchars($p['descricao_item'] ?? '') ?></small>
                                        </td>
                                        <td><span class="badge bg-secondary bg-opacity-50"><?= htmlspecialchars($p['nome_cat'] ?? 'Geral') ?></span></td>
                                        <td class="text-success fw-bold">R$ <?= number_format($p['preco_item'], 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $p['disponivel'] ? 'success' : 'danger' ?> bg-opacity-25 text-<?= $p['disponivel'] ? 'success' : 'danger' ?> border border-<?= $p['disponivel'] ? 'success' : 'danger' ?> border-opacity-25 rounded-pill px-3">
                                                <?= $p['disponivel'] ? 'Disponível' : 'Pausado' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-warning btn-action text-dark me-1" 
                                                    onclick="editarProduto(<?= htmlspecialchars(json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-action" 
                                                    onclick="confirmarExclusao(<?= $p['id'] ?>, '<?= addslashes($p['nome_item']) ?>')" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

<!-- MODAL CADASTRO / EDIÇÃO DE PRODUTO -->
<div class="modal fade" id="modalProduto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-4 text-white p-4 shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="modalTitulo">Cadastrar Novo Produto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formProduto" onsubmit="salvarProdutoAjax(event)" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id_item" id="prod_id" value="0">

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">NOME DO PRODUTO *</label>
                    <input type="text" name="nome_item" id="prod_nome" class="form-control" placeholder="Ex: X-Bacon Artesanal" required>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">CATEGORIA *</label>
                    <select name="id_cat" id="prod_cat" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome_cat']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="small fw-bold text-secondary mb-1">PREÇO DE VENDA (R$) *</label>
                        <input type="text" name="preco_item" id="prod_preco" class="form-control" placeholder="28,50" required>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-secondary mb-1">FOTO DO LANCHE</label>
                        <input type="file" name="imagem" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">INGREDIENTES / DESCRIÇÃO</label>
                    <textarea name="descricao_item" id="prod_desc" class="form-control" rows="2" placeholder="Blend 180g, queijo cheddar, bacon crocante, cebola caramelizada..."></textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="disponivel" id="prod_disp" checked>
                    <label class="form-check-label small fw-bold" for="prod_disp">Item disponível para venda imediata</label>
                </div>

                <button type="submit" id="btnSalvarProd" class="btn btn-success w-100 rounded-pill fw-bold py-3 shadow">
                    <i class="fas fa-save me-1"></i> Salvar Produto
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let modalInst = null;

function abrirModalProduto() {
    document.getElementById('formProduto').reset();
    document.getElementById('prod_id').value = "0";
    document.getElementById('prod_disp').checked = true;
    document.getElementById('modalTitulo').innerText = "Cadastrar Novo Produto";
    
    modalInst = new bootstrap.Modal(document.getElementById('modalProduto'));
    modalInst.show();
}

function editarProduto(p) {
    document.getElementById('prod_id').value = p.id;
    document.getElementById('prod_nome').value = p.nome_item;
    document.getElementById('prod_cat').value = p.id_cat;
    document.getElementById('prod_preco').value = parseFloat(p.preco_item).toFixed(2).replace('.', ',');
    document.getElementById('prod_desc').value = p.descricao_item || '';
    document.getElementById('prod_disp').checked = (parseInt(p.disponivel) === 1);
    document.getElementById('modalTitulo').innerText = "Editar Produto: " + p.nome_item;

    modalInst = new bootstrap.Modal(document.getElementById('modalProduto'));
    modalInst.show();
}

function salvarProdutoAjax(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSalvarProd');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

    const form = document.getElementById('formProduto');
    const formData = new FormData(form);

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Produto';

        if (res.sucesso) {
            modalInst.hide();
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: res.mensagem,
                background: '#1b2e4b',
                color: '#fff',
                confirmButtonColor: '#34af23',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: res.mensagem || 'Falha ao salvar produto.',
                background: '#1b2e4b',
                color: '#fff',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Produto';
        window.location.reload();
    });
}

function confirmarExclusao(id, nome) {
    Swal.fire({
        title: 'Excluir Produto?',
        text: `Tem certeza que deseja deletar o produto "${nome}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, Deletar!',
        cancelButtonText: 'Não, Cancelar',
        background: '#1b2e4b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('acao', 'excluir');
            formData.append('id', id);

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.sucesso) {
                    const row = document.getElementById('linha-prod-' + id);
                    if (row) row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Deletado!',
                        text: res.mensagem,
                        background: '#1b2e4b',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: res.mensagem,
                        background: '#1b2e4b',
                        color: '#fff'
                    });
                }
            })
            .catch(() => window.location.reload());
        }
    });
}
</script>
</body>
</html>