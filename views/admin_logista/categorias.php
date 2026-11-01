<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Categorias | <?= htmlspecialchars($loja['user_name']) ?></title>

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
        .form-control { background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 12px; padding: 12px 15px; }
        .form-control:focus { background-color: rgba(255,255,255,0.08); border-color: #34af23; color: #fff; box-shadow: none; }
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR COMPLETA (PADRÃO UNIFICADO) -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($loja['user_name']) ?></h5>
                    <span class="badge bg-success bg-opacity-20 text-success small mt-1">Loja Aberta</span>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin"><i class="fas fa-columns me-2"></i> Pedidos (Kanban)</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=categorias"><i class="fas fa-tags me-2"></i> Categorias</a>
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
            <h3 class="fw-bold mb-1">Categorias do Cardápio</h3>
            <p class="text-secondary small mb-4">Organize seus produtos por seções (ex: Hambúrgueres, Bebidas, Sobremesas).</p>

            <div class="row g-4">

                <!-- FORMULÁRIO DE CADASTRO / EDIÇÃO -->
                <div class="col-md-4">
                    <div class="card card-custom p-4 shadow">
                        <h5 class="fw-bold mb-3" id="formCatTitulo">Nova Categoria</h5>
                        <form id="formCategoria" onsubmit="salvarCategoriaAjax(event)">
                            <input type="hidden" name="acao" value="salvar">
                            <input type="hidden" name="id_cat" id="cat_id" value="0">

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1">NOME DA CATEGORIA *</label>
                                <input type="text" name="nome_cat" id="cat_nome" class="form-control" placeholder="Ex: Bebidas, Pizzas..." required>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold text-secondary mb-1">DESCRIÇÃO (OPCIONAL)</label>
                                <textarea name="desc_cat" id="cat_desc" class="form-control" rows="3" placeholder="Breve texto exibido no cardápio"></textarea>
                            </div>

                            <button type="submit" id="btnSalvarCat" class="btn btn-success w-100 rounded-pill fw-bold py-2 mb-2">
                                <i class="fas fa-save me-1"></i> Salvar Categoria
                            </button>
                            <button type="button" id="btnCancelarEdicao" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" style="display:none;" onclick="cancelarEdicaoCat()">
                                Cancelar Edição
                            </button>
                        </form>
                    </div>
                </div>

                <!-- TABELA DE CATEGORIAS -->
                <div class="col-md-8">
                    <div class="card card-custom p-4 shadow">
                        <h5 class="fw-bold mb-3">Categorias Cadastradas</h5>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead>
                                    <tr class="text-secondary small">
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tabela-categorias">
                                    <?php if (empty($categorias)): ?>
                                        <tr><td colspan="3" class="text-center py-4 text-secondary">Nenhuma categoria cadastrada.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($categorias as $cat): ?>
                                            <tr id="linha-cat-<?= $cat['id'] ?>">
                                                <td class="fw-bold text-white"><?= htmlspecialchars($cat['nome_cat']) ?></td>
                                                <td class="text-secondary small"><?= htmlspecialchars($cat['desc_cat']) ?></td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-warning btn-action text-dark me-1" 
                                                            onclick="editarCategoria(<?= htmlspecialchars(json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Editar">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-action" 
                                                            onclick="confirmarExclusaoCat(<?= $cat['id'] ?>, '<?= addslashes($cat['nome_cat']) ?>')" title="Excluir">
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
                </div>

            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function editarCategoria(cat) {
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_nome').value = cat.nome_cat;
    document.getElementById('cat_desc').value = cat.desc_cat;
    document.getElementById('formCatTitulo').innerText = "Editar Categoria";
    document.getElementById('btnCancelarEdicao').style.display = "block";
    document.getElementById('cat_nome').focus();
}

function cancelarEdicaoCat() {
    document.getElementById('formCategoria').reset();
    document.getElementById('cat_id').value = "0";
    document.getElementById('formCatTitulo').innerText = "Nova Categoria";
    document.getElementById('btnCancelarEdicao').style.display = "none";
}

function salvarCategoriaAjax(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSalvarCat');
    btn.disabled = true;

    const formData = new FormData(document.getElementById('formCategoria'));

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        if (res.sucesso) {
            cancelarEdicaoCat();
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: res.mensagem,
                background: '#1b2e4b',
                color: '#fff',
                confirmButtonColor: '#34af23',
                confirmButtonText: 'OK'
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: res.mensagem || 'Falha ao salvar categoria.',
                background: '#1b2e4b',
                color: '#fff'
            });
        }
    })
    .catch(() => {
        btn.disabled = false;
        window.location.reload();
    });
}

function confirmarExclusaoCat(id, nome) {
    Swal.fire({
        title: 'Excluir Categoria?',
        text: `Tem certeza que deseja deletar a categoria "${nome}"?`,
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
                    const row = document.getElementById('linha-cat-' + id);
                    if (row) row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Deletada!',
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