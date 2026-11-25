<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Adicionais & Molhos | <?= htmlspecialchars($loja['user_name']) ?></title>

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
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; }
        
        .adic-thumb { width: 48px; height: 48px; border-radius: 12px; object-fit: cover; }
        .adic-thumb-fallback {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center;
            color: #888ea8; font-size: 1.2rem; border: 1px dashed rgba(255,255,255,0.15);
        }
        .preview-box-modal {
            width: 80px; height: 80px; border-radius: 16px;
            background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; margin: 0 auto 10px;
        }
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
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=categorias"><i class="fas fa-tags me-2"></i> Categorias</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=adicionais"><i class="fas fa-plus-circle me-2"></i> Adicionais & Molhos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes"><i class="fas fa-users me-2"></i> Meus Clientes (CRM)</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=usuarios"><i class="fas fa-user-shield me-2"></i> Equipe / Usuários</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=vagas"><i class="fas fa-briefcase me-2"></i> Vagas de Emprego</a>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Adicionais & Molhos com Fotos</h3>
                    <p class="text-secondary small mb-0">Cadastre fotos de sachês, bisnagas e complementos para enriquecer o cardápio no celular do cliente.</p>
                </div>
                <button class="btn btn-success rounded-pill fw-bold px-4 py-2" onclick="abrirModalAdicional()">
                    <i class="fas fa-plus me-1"></i> Novo Item
                </button>
            </div>

            <div class="card card-custom p-4 shadow">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Tipo de Exibição</th>
                                <th>Valor</th>
                                <th>Medida</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-adicionais">
                            <?php if (empty($adicionais)): ?>
                                <tr><td colspan="7" class="text-center py-4 text-secondary">Nenhum adicional ou molho cadastrado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($adicionais as $a): 
                                    $val = (float)$a['valor_adicional'];
                                    $isMolho = (trim($a['categorias_adicional']) === 'molho');
                                    $temFoto = false;
                                    $imgUrl = '';
                                    if (!empty($a['img_adicional'])) {
                                        $caminhoFisico = __DIR__ . '/../../uploads/' . $a['img_adicional'];
                                        if (file_exists($caminhoFisico)) {
                                            $temFoto = true;
                                            $imgUrl = '/uploads/' . htmlspecialchars($a['img_adicional']);
                                        }
                                    }
                                ?>
                                    <tr id="linha-adic-<?= $a['id_adicionais'] ?>">
                                        <td>
                                            <?php if ($temFoto): ?>
                                                <img src="<?= $imgUrl ?>" class="adic-thumb shadow-sm" alt="Foto"
                                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'adic-thumb-fallback\'><i class=\'fas fa-pepper-hot\'></i></div>';">
                                            <?php else: ?>
                                                <div class="adic-thumb-fallback">
                                                    <i class="fas <?= $isMolho ? 'fa-pepper-hot text-danger' : 'fa-bacon text-warning' ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <b class="text-white"><?= htmlspecialchars($a['nome_adicional']) ?></b>
                                        </td>
                                        <td>
                                            <?php if ($isMolho): ?>
                                                <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                                                    <i class="fas fa-pepper-hot me-1"></i> Molho do Carrinho (Até 2 Cortesias)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 rounded-pill px-3">
                                                    <i class="fas fa-hamburger me-1"></i> Adicional de Lanche
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($val <= 0): ?>
                                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3">Grátis</span>
                                            <?php else: ?>
                                                <span class="text-success fw-bold">+ R$ <?= number_format($val, 2, ',', '.') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-secondary bg-opacity-50"><?= htmlspecialchars($a['medida_adicional'] ?: 'UN') ?></span></td>
                                        <td>
                                            <span class="badge bg-<?= $a['status_adicional'] ? 'success' : 'danger' ?> bg-opacity-25 text-<?= $a['status_adicional'] ? 'success' : 'danger' ?> border border-<?= $a['status_adicional'] ? 'success' : 'danger' ?> border-opacity-25 rounded-pill px-3">
                                                <?= $a['status_adicional'] ? 'Disponível' : 'Pausado' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-warning btn-action text-dark me-1" 
                                                    onclick="editarAdicional(<?= htmlspecialchars(json_encode($a, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-action" 
                                                    onclick="confirmarExclusaoAdicional(<?= $a['id_adicionais'] ?>, '<?= addslashes($a['nome_adicional']) ?>')" title="Excluir">
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

<!-- MODAL CADASTRO / EDIÇÃO COM UPLOAD DE FOTO -->
<div class="modal fade" id="modalAdicional" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-4 text-white p-4 shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="modalAdicTitulo">Novo Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAdicional" onsubmit="salvarAdicionalAjax(event)" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id_adicionais" id="adic_id" value="0">

                <!-- TIPO DE ITEM -->
                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-2">TIPO DE ITEM *</label>
                    <div class="d-flex gap-2">
                        <div class="form-check p-3 rounded-3 border border-secondary flex-grow-1" style="cursor: pointer;" onclick="selecionarTipoItem('adicional')">
                            <input class="form-check-input" type="radio" name="tipo_item" id="tipo_adicional" value="adicional" checked onchange="atualizarInterfaceTipo()">
                            <label class="form-check-label small fw-bold text-warning" for="tipo_adicional">
                                <i class="fas fa-hamburger me-1"></i> Adicional de Lanche<br>
                                <small class="text-secondary fw-normal">Bacon, Ovo, Cheddar...</small>
                            </label>
                        </div>
                        <div class="form-check p-3 rounded-3 border border-secondary flex-grow-1" style="cursor: pointer;" onclick="selecionarTipoItem('molho')">
                            <input class="form-check-input" type="radio" name="tipo_item" id="tipo_molho" value="molho" onchange="atualizarInterfaceTipo()">
                            <label class="form-check-label small fw-bold text-danger" for="tipo_molho">
                                <i class="fas fa-pepper-hot me-1"></i> Molho do Carrinho<br>
                                <small class="text-secondary fw-normal">Sachês / Bisnagas (Até 2 grátis)</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- PREVIEW E UPLOAD DA FOTO DO SACHÊ / BISNAGA -->
                <div class="mb-3 text-center">
                    <label class="small fw-bold text-secondary mb-2 d-block">FOTO DO ADICIONAL / SACHÊ / BISNAGA</label>
                    <div class="preview-box-modal" id="container-foto-adic">
                        <img id="img-adic-preview" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <i class="fas fa-camera fa-2x text-secondary" id="icon-adic-fallback"></i>
                    </div>
                    <input type="file" name="img_adicional" id="input_img_adic" class="form-control form-control-sm" accept="image/*" onchange="previewFotoAdicional(this)">
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">NOME DO ITEM *</label>
                    <input type="text" name="nome_adicional" id="adic_nome" class="form-control" placeholder="Ex: Sachê Maionese Especial, Bacon Crocante..." required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="small fw-bold text-secondary mb-1">VALOR (R$) *</label>
                        <input type="text" name="valor_adicional" id="adic_valor" class="form-control" placeholder="0,00" required>
                        <small class="text-secondary" id="helpValor" style="font-size: 11px;">Para molhos, os 2 primeiros são cortesia da casa.</small>
                    </div>
                    <div class="col-5">
                        <label class="small fw-bold text-secondary mb-1">MEDIDA</label>
                        <select name="medida_adicional" id="adic_medida" class="form-select">
                            <option value="SACHÊ">SACHÊ</option>
                            <option value="BISNAGA">BISNAGA</option>
                            <option value="POTINHO">POTINHO</option>
                            <option value="UN">UN</option>
                            <option value="PORÇÃO">PORÇÃO</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3" id="bloco-categorias-lanche">
                    <label class="small fw-bold text-secondary mb-1">EM QUAIS CATEGORIAS DE LANCHES ESSE ADICIONAL APARECE?</label>
                    <div class="p-3 bg-secondary bg-opacity-10 rounded-3" style="max-height: 140px; overflow-y: auto;">
                        <?php foreach ($categorias as $c): ?>
                            <div class="form-check form-check-inline mb-1">
                                <input class="form-check-input cat-item-check" type="checkbox" name="categorias_adicional[]" value="<?= $c['id'] ?>" id="cat_<?= $c['id'] ?>">
                                <label class="form-check-label small text-white" for="cat_<?= $c['id'] ?>"><?= htmlspecialchars($c['nome_cat']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="status_adicional" id="adic_status" checked>
                    <label class="form-check-label small fw-bold" for="adic_status">Disponível para os clientes</label>
                </div>

                <button type="submit" id="btnSalvarAdic" class="btn btn-success w-100 rounded-pill fw-bold py-3 shadow">
                    <i class="fas fa-save me-1"></i> Salvar Item
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let modalAdicInst = null;

function previewFotoAdicional(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('img-adic-preview');
            const icon = document.getElementById('icon-adic-fallback');
            img.src = e.target.result;
            img.style.display = 'block';
            icon.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function selecionarTipoItem(tipo) {
    document.getElementById('tipo_' + tipo).checked = true;
    atualizarInterfaceTipo();
}

function atualizarInterfaceTipo() {
    const isMolho = document.getElementById('tipo_molho').checked;
    const blocoCats = document.getElementById('bloco-categorias-lanche');
    const helpValor = document.getElementById('helpValor');

    if (isMolho) {
        blocoCats.style.display = 'none';
        helpValor.innerText = "Para molhos, os 2 primeiros são cortesia da casa. Do 3º em diante será cobrado este valor.";
    } else {
        blocoCats.style.display = 'block';
        helpValor.innerText = "Valor cobrado por cada adicional no lanche.";
    }
}

function abrirModalAdicional() {
    document.getElementById('formAdicional').reset();
    document.getElementById('adic_id').value = "0";
    document.getElementById('tipo_adicional').checked = true;
    document.getElementById('adic_status').checked = true;
    document.getElementById('img-adic-preview').style.display = 'none';
    document.getElementById('icon-adic-fallback').style.display = 'block';
    document.getElementById('modalAdicTitulo').innerText = "Novo Item";
    atualizarInterfaceTipo();

    modalAdicInst = new bootstrap.Modal(document.getElementById('modalAdicional'));
    modalAdicInst.show();
}

function editarAdicional(a) {
    document.getElementById('adic_id').value = a.id_adicionais;
    document.getElementById('adic_nome').value = a.nome_adicional;
    document.getElementById('adic_valor').value = parseFloat(a.valor_adicional).toFixed(2).replace('.', ',');
    document.getElementById('adic_medida').value = a.medida_adicional || 'UN';
    document.getElementById('adic_status').checked = (parseInt(a.status_adicional) === 1);
    document.getElementById('modalAdicTitulo').innerText = "Editar: " + a.nome_adicional;

    // Foto atual se existir
    const imgPreview = document.getElementById('img-adic-preview');
    const iconFallback = document.getElementById('icon-adic-fallback');
    if (a.img_adicional) {
        imgPreview.src = '/uploads/' + a.img_adicional;
        imgPreview.style.display = 'block';
        iconFallback.style.display = 'none';
    } else {
        imgPreview.style.display = 'none';
        iconFallback.style.display = 'block';
    }

    const isMolho = (String(a.categorias_adicional || '').trim() === 'molho');
    if (isMolho) {
        document.getElementById('tipo_molho').checked = true;
    } else {
        document.getElementById('tipo_adicional').checked = true;
        const cats = String(a.categorias_adicional || '').split(',').map(s => s.trim());
        document.querySelectorAll('.cat-item-check').forEach(c => {
            c.checked = cats.includes(String(c.value));
        });
    }

    atualizarInterfaceTipo();
    modalAdicInst = new bootstrap.Modal(document.getElementById('modalAdicional'));
    modalAdicInst.show();
}

function salvarAdicionalAjax(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSalvarAdic');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

    const formData = new FormData(document.getElementById('formAdicional'));

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar';

        if (res.sucesso) {
            modalAdicInst.hide();
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
                title: 'Atenção',
                text: res.mensagem || 'Falha ao salvar item.',
                background: '#1b2e4b',
                color: '#fff',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(() => {
        btn.disabled = false;
        window.location.reload();
    });
}

function confirmarExclusaoAdicional(id, nome) {
    Swal.fire({
        title: 'Excluir Item?',
        text: `Deseja remover "${nome}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, Remover!',
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
                    const row = document.getElementById('linha-adic-' + id);
                    if (row) row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Removido!',
                        text: res.mensagem,
                        background: '#1b2e4b',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
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