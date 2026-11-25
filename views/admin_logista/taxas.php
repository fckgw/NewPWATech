<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taxas de Entrega | <?= htmlspecialchars($loja['user_name']) ?></title>

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
        
        /* SIDEBAR COMPLETA E PADRONIZADA */
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; border-right: 1px solid rgba(255,255,255,0.05); }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }

        .card-custom { background: var(--bg-card); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .form-control, .form-select { background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 12px; padding: 12px 15px; }
        .form-control:focus, .form-select:focus { background-color: rgba(255,255,255,0.08); border-color: #34af23; color: #fff; box-shadow: none; }
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; }
        
        /* PAGINAÇÃO */
        .pagination .page-link { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; margin: 0 3px; font-weight: 600; }
        .pagination .page-item.active .page-link { background: #34af23; border-color: #34af23; color: #fff; }
        .pagination .page-item.disabled .page-link { opacity: 0.4; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR COMPLETA COM TODOS OS ITENS -->
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
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=adicionais"><i class="fas fa-plus-circle me-2"></i> Adicionais & Molhos</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes"><i class="fas fa-users me-2"></i> Meus Clientes (CRM)</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=usuarios"><i class="fas fa-user-shield me-2"></i> Equipe / Usuários</a>
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
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Taxas de Entrega por Bairro</h3>
                    <p class="text-secondary small mb-0">Gerencie as regiões atendidas pelo seu delivery e a cobrança da taxa para cada local.</p>
                </div>
                <button class="btn btn-success rounded-pill fw-bold px-4 py-2" onclick="abrirModalBairro()">
                    <i class="fas fa-plus me-1"></i> Cadastrar Bairro
                </button>
            </div>

            <!-- CARD DO GRID COM FILTRO E PAGINAÇÃO -->
            <div class="card card-custom p-4 shadow">
                
                <!-- BARRA DE PESQUISA -->
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary bg-opacity-10 border-0 text-secondary">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="filtro_pesquisa" class="form-control" placeholder="Pesquisar por bairro ou cidade..." onkeyup="filtrarBairrosGrid()">
                        </div>
                    </div>
                    <div class="col-md-7 text-md-end text-secondary small">
                        <span id="info_contagem">Carregando bairros...</span>
                    </div>
                </div>

                <!-- TABELA GRID -->
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Bairro</th>
                                <th>Cidade / Estado</th>
                                <th>Taxa de Entrega</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-bairros-corpo">
                            <!-- Injetado dinamicamente via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- CONTROLES DE PAGINAÇÃO (10 EM 10) -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3 border-top border-secondary border-opacity-10">
                    <div class="text-secondary small" id="info_paginacao">
                        Página 1 de 1
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginacao_controles">
                            <!-- Botões de página injetados via JS -->
                        </ul>
                    </nav>
                </div>

            </div>
        </main>
    </div>
</div>

<!-- MODAL CADASTRO / EDIÇÃO DE BAIRRO -->
<div class="modal fade" id="modalBairro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-4 text-white p-4 shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="modalBairroTitulo">Cadastrar Bairro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formBairro" onsubmit="salvarBairroAjax(event)">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id_bairro" id="b_id" value="0">

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">NOME DO BAIRRO *</label>
                    <input type="text" name="bairro" id="b_bairro" class="form-control" placeholder="Ex: Centro, Vila Nova, Jardim das Flores..." required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="small fw-bold text-secondary mb-1">CIDADE *</label>
                        <input type="text" name="cidade" id="b_cidade" class="form-control" value="Caçapava" required>
                    </div>
                    <div class="col-4">
                        <label class="small fw-bold text-secondary mb-1">UF *</label>
                        <input type="text" name="uf" id="b_uf" class="form-control" maxlength="2" value="SP" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold text-secondary mb-1">VALOR DA TAXA DE ENTREGA (R$) *</label>
                    <input type="text" name="taxa" id="b_taxa" class="form-control fs-5 fw-bold text-success" placeholder="0,00" required>
                    <small class="text-secondary" style="font-size: 11px;">Coloque 0,00 para bairros com entrega gratuita.</small>
                </div>

                <button type="submit" id="btnSalvarBairro" class="btn btn-success w-100 rounded-pill fw-bold py-3 shadow">
                    <i class="fas fa-save me-1"></i> Salvar Bairro
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Dados vindos do PHP convertidos para Javascript
const todosBairros = <?= json_encode($bairros, JSON_UNESCAPED_UNICODE) ?>;
let bairrosFiltrados = [...todosBairros];

// Configurações de Paginação (10 em 10)
const ITENS_POR_PAGINA = 10;
let paginaAtual = 1;
let modalBairroInst = null;

$(document).ready(function() {
    renderizarGridPaginado();
});

// FILTRO DE BUSCA INSTANTÂNEA
function filtrarBairrosGrid() {
    const termo = document.getElementById('filtro_pesquisa').value.toLowerCase().trim();
    
    if (termo === "") {
        bairrosFiltrados = [...todosBairros];
    } else {
        bairrosFiltrados = todosBairros.filter(b => 
            (b.bairro || '').toLowerCase().includes(termo) ||
            (b.cidade || '').toLowerCase().includes(termo)
        );
    }
    paginaAtual = 1;
    renderizarGridPaginado();
}

// RENDERIZAÇÃO DO GRID COM PAGINAÇÃO DE 10 EM 10
function renderizarGridPaginado() {
    const tbody = document.getElementById('tabela-bairros-corpo');
    const totalItens = bairrosFiltrados.length;
    const totalPaginas = Math.ceil(totalItens / ITENS_POR_PAGINA) || 1;

    if (paginaAtual > totalPaginas) paginaAtual = totalPaginas;

    const inicio = (paginaAtual - 1) * ITENS_POR_PAGINA;
    const fim = Math.min(inicio + ITENS_POR_PAGINA, totalItens);
    const itensPagina = bairrosFiltrados.slice(inicio, fim);

    if (totalItens === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-secondary">Nenhum bairro encontrado.</td></tr>`;
        document.getElementById('info_contagem').innerText = "0 bairros encontrados";
        document.getElementById('info_paginacao').innerText = "Página 0 de 0";
        document.getElementById('paginacao_controles').innerHTML = "";
        return;
    }

    let html = "";
    itensPagina.forEach(b => {
        const valTaxa = parseFloat(b.taxa || 0);
        const bJson = JSON.stringify(b).replace(/"/g, '&quot;');

        html += `
        <tr id="linha-bairro-${b.id}">
            <td>
                <b class="text-white">${b.bairro}</b>
            </td>
            <td>
                <span class="text-secondary">${b.cidade} / <b class="text-white">${b.uf}</b></span>
            </td>
            <td>
                ${valTaxa <= 0 
                    ? '<span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3">Entrega Grátis</span>'
                    : `<span class="text-success fw-bold fs-6">R$ ${valTaxa.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>`
                }
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-warning btn-action text-dark me-1" onclick="editarBairro(${bJson})" title="Editar">
                    <i class="fas fa-pen"></i>
                </button>
                <button type="button" class="btn btn-danger btn-action" onclick="confirmarExclusaoBairro(${b.id}, '${b.bairro.replace(/'/g, "\\'")}')" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html;

    // Atualiza contadores
    document.getElementById('info_contagem').innerText = `Exibindo ${inicio + 1} a ${fim} de ${totalItens} bairros cadastrados`;
    document.getElementById('info_paginacao').innerText = `Página ${paginaAtual} de ${totalPaginas}`;

    // Monta controles de paginação
    renderizarControlesPaginacao(totalPaginas);
}

function renderizarControlesPaginacao(totalPaginas) {
    const ul = document.getElementById('paginacao_controles');
    let html = "";

    // Botão Anterior
    html += `
    <li class="page-item ${paginaAtual === 1 ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="mudarPagina(${paginaAtual - 1})"><i class="fas fa-chevron-left"></i></a>
    </li>`;

    // Botões numéricos
    for (let i = 1; i <= totalPaginas; i++) {
        // Mostra páginas próximas da atual para não poluir
        if (i === 1 || i === totalPaginas || (i >= paginaAtual - 1 && i <= paginaAtual + 1)) {
            html += `
            <li class="page-item ${i === paginaAtual ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="mudarPagina(${i})">${i}</a>
            </li>`;
        } else if (i === paginaAtual - 2 || i === paginaAtual + 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Botão Próximo
    html += `
    <li class="page-item ${paginaAtual === totalPaginas ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="mudarPagina(${paginaAtual + 1})"><i class="fas fa-chevron-right"></i></a>
    </li>`;

    ul.innerHTML = html;
}

function mudarPagina(novaPagina) {
    const totalPaginas = Math.ceil(bairrosFiltrados.length / ITENS_POR_PAGINA) || 1;
    if (novaPagina < 1 || novaPagina > totalPaginas) return;
    paginaAtual = novaPagina;
    renderizarGridPaginado();
}

// MODAL ABERTURA / CADASTRO
function abrirModalBairro() {
    document.getElementById('formBairro').reset();
    document.getElementById('b_id').value = "0";
    document.getElementById('b_cidade').value = "Caçapava";
    document.getElementById('b_uf').value = "SP";
    document.getElementById('modalBairroTitulo').innerText = "Cadastrar Bairro";

    modalBairroInst = new bootstrap.Modal(document.getElementById('modalBairro'));
    modalBairroInst.show();
}

// MODAL EDIÇÃO
function editarBairro(b) {
    document.getElementById('b_id').value = b.id;
    document.getElementById('b_bairro').value = b.bairro;
    document.getElementById('b_cidade').value = b.cidade || "Caçapava";
    document.getElementById('b_uf').value = b.uf || "SP";
    document.getElementById('b_taxa').value = parseFloat(b.taxa || 0).toFixed(2).replace('.', ',');
    document.getElementById('modalBairroTitulo').innerText = "Editar Bairro: " + b.bairro;

    modalBairroInst = new bootstrap.Modal(document.getElementById('modalBairro'));
    modalBairroInst.show();
}

// SALVAR VIA AJAX
function salvarBairroAjax(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSalvarBairro');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

    const formData = new FormData(document.getElementById('formBairro'));

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Bairro';

        if (res.sucesso) {
            modalBairroInst.hide();
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
                text: res.mensagem || 'Falha ao salvar bairro.',
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

// EXCLUIR COM CONFIRMAÇÃO
function confirmarExclusaoBairro(id, nome) {
    Swal.fire({
        title: 'Excluir Bairro?',
        text: `Deseja remover "${nome}" das taxas de entrega?`,
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Removido!',
                        text: res.mensagem,
                        background: '#1b2e4b',
                        color: '#fff',
                        timer: 1800,
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