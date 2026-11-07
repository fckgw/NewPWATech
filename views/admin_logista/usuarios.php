<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuários da Loja | <?= htmlspecialchars($loja['user_name']) ?></title>

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
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR COMPLETA COM O NOVO MENU USUÁRIOS -->
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
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes"><i class="fas fa-users me-2"></i> Meus Clientes (CRM)</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=usuarios"><i class="fas fa-user-shield me-2"></i> Equipe / Usuários</a>
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
                    <h3 class="fw-bold mb-1">Equipe e Usuários da Loja</h3>
                    <p class="text-secondary small mb-0">Gerencie os acessos de atendentes, motoboys e administradores da sua lanchonete.</p>
                </div>
                <div>
                    <span class="badge bg-secondary p-2 px-3 rounded-pill me-2">
                        <?= count($usuarios) ?> / 3 Usuários Utilizados
                    </span>
                    <button class="btn btn-success rounded-pill fw-bold px-4 py-2" onclick="abrirModalUsuario()" <?= count($usuarios) >= 3 ? 'disabled title="Limite de 3 usuários atingido"' : '' ?>>
                        <i class="fas fa-user-plus me-1"></i> Novo Usuário
                    </button>
                </div>
            </div>

            <?php if (count($usuarios) >= 3): ?>
                <div class="alert alert-warning border-0 rounded-4 py-2 px-3 small mb-4">
                    <i class="fas fa-info-circle me-1"></i> Você atingiu o limite de <b>3 usuários</b> cadastrados para a sua loja.
                </div>
            <?php endif; ?>

            <div class="card card-custom p-4 shadow">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary small">
                                <th>Nome Completo</th>
                                <th>E-mail (Login)</th>
                                <th>Telefone / CPF</th>
                                <th>Operação / Nível</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-usuarios">
                            <?php foreach ($usuarios as $u): 
                                $isTitular = ((int)$u['user_id'] === (int)$loja['user_id']);
                                $nivelTxt = 'Usuário Comum';
                                $nivelBadge = 'bg-secondary';
                                if ((int)$u['user_level'] === 2) {
                                    $nivelTxt = 'Motoboy / Entregador';
                                    $nivelBadge = 'bg-info text-dark';
                                } elseif ((int)$u['user_level'] === 3) {
                                    $nivelTxt = 'Administrador da Loja';
                                    $nivelBadge = 'bg-warning text-dark';
                                }
                            ?>
                                <tr id="linha-user-<?= $u['user_id'] ?>">
                                    <td>
                                        <b class="text-white"><?= htmlspecialchars($u['user_name']) ?></b>
                                        <?php if ($isTitular): ?>
                                            <span class="badge bg-success bg-opacity-25 text-success rounded-pill ms-1" style="font-size: 10px;">Proprietário</span>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-secondary">Criado em: <?= date('d/m/Y', strtotime($u['user_registration'])) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($u['user_email']) ?></td>
                                    <td>
                                        <div><i class="fab fa-whatsapp text-success me-1"></i> <?= htmlspecialchars($u['user_telefone'] ?: 'Não informado') ?></div>
                                        <small class="text-secondary">CPF: <?= htmlspecialchars($u['user_cpf'] ?: 'Não informado') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?= $nivelBadge ?> rounded-pill px-3 py-1">
                                            <?= $nivelTxt ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-2">
                                            <?= htmlspecialchars($u['user_status'] ?: 'ativo') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-warning btn-action text-dark me-1" 
                                                onclick="editarUsuario(<?= htmlspecialchars(json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <?php if (!$isTitular): ?>
                                            <button type="button" class="btn btn-danger btn-action" 
                                                    onclick="confirmarExclusaoUsuario(<?= $u['user_id'] ?>, '<?= addslashes($u['user_name']) ?>')" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- MODAL CADASTRO / EDIÇÃO DE USUÁRIO -->

<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-4 text-white p-4 shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" id="modalUserTitulo">Novo Usuário da Loja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formUsuario" onsubmit="salvarUsuarioAjax(event)">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="user_id" id="u_id" value="0">

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">NOME COMPLETO *</label>
                    <input type="text" name="user_name" id="u_nome" class="form-control" placeholder="Ex: Carlos Silva" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="small fw-bold text-secondary mb-1">CPF</label>
                        <input type="text" name="user_cpf" id="u_cpf" class="form-control" placeholder="000.000.000-00">
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-secondary mb-1">TELEFONE / WHATSAPP</label>
                        <input type="tel" name="user_telefone" id="u_tel" class="form-control" placeholder="(00) 00000-0000">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">OPERAÇÃO / NÍVEL DE ACESSO *</label>
                    <select name="user_level" id="u_level" class="form-select" required>
                        <option value="1">Usuário Comum (Atendente / Caixa)</option>
                        <option value="2">Motoboy / Entregador</option>
                        <option value="3">Administrador da Loja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">E-MAIL DE ACESSO (LOGIN) *</label>
                    <input type="email" name="user_email" id="u_email" class="form-control" placeholder="usuario@lanchonete.com" required>
                </div>

                <!-- CAMPO SENHA COM OLHO -->
                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1" id="labelSenha">SENHA DE ACESSO *</label>
                    <div class="input-group">
                        <input type="password" name="user_password" id="u_pass" class="form-control" placeholder="••••••••">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassVisibility('u_pass', 'eyePass1')">
                            <i class="fas fa-eye text-secondary" id="eyePass1"></i>
                        </button>
                    </div>
                    <small class="text-secondary" id="helpSenha" style="font-size: 11px; display: none;">Deixe em branco caso não queira alterar a senha.</small>
                </div>

                <!-- CAMPO CONFIRMAR SENHA COM OLHO -->
                <div class="mb-4">
                    <label class="small fw-bold text-secondary mb-1" id="labelConfirmaSenha">CONFIRMAR SENHA *</label>
                    <div class="input-group">
                        <input type="password" name="user_password_confirm" id="u_pass_confirm" class="form-control" placeholder="••••••••">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassVisibility('u_pass_confirm', 'eyePass2')">
                            <i class="fas fa-eye text-secondary" id="eyePass2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btnSalvarUser" class="btn btn-success w-100 rounded-pill fw-bold py-3 shadow">
                    <i class="fas fa-save me-1"></i> Salvar Usuário
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
let modalUserInst = null;

$(document).ready(function() {
    $('#u_cpf').mask('000.000.000-00');
    $('#u_tel').mask('(00) 00000-0000');
});

function togglePassVisibility(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function abrirModalUsuario() {
    document.getElementById('formUsuario').reset();
    document.getElementById('u_id').value = "0";
    document.getElementById('modalUserTitulo').innerText = "Novo Usuário da Loja";
    document.getElementById('u_pass').required = true;
    document.getElementById('u_pass_confirm').required = true;
    document.getElementById('labelSenha').innerText = "SENHA DE ACESSO *";
    document.getElementById('labelConfirmaSenha').innerText = "CONFIRMAR SENHA *";
    document.getElementById('helpSenha').style.display = "none";

    modalUserInst = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modalUserInst.show();
}

function editarUsuario(u) {
    document.getElementById('u_id').value = u.user_id;
    document.getElementById('u_nome').value = u.user_name;
    document.getElementById('u_cpf').value = u.user_cpf || '';
    document.getElementById('u_tel').value = u.user_telefone || '';
    document.getElementById('u_email').value = u.user_email;
    document.getElementById('u_level').value = u.user_level;
    document.getElementById('u_pass').value = '';
    document.getElementById('u_pass_confirm').value = '';
    document.getElementById('u_pass').required = false;
    document.getElementById('u_pass_confirm').required = false;
    document.getElementById('labelSenha').innerText = "NOVA SENHA (OPCIONAL)";
    document.getElementById('labelConfirmaSenha').innerText = "CONFIRMAR NOVA SENHA";
    document.getElementById('helpSenha').style.display = "block";
    document.getElementById('modalUserTitulo').innerText = "Editar Usuário: " + u.user_name;

    modalUserInst = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modalUserInst.show();
}

function salvarUsuarioAjax(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSalvarUser');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

    const form = document.getElementById('formUsuario');
    const formData = new FormData(form);

    fetch(window.location.href, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Usuário';

        if (res.sucesso) {
            modalUserInst.hide();
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
                title: 'Atenção',
                text: res.mensagem || 'Falha ao salvar usuário.',
                background: '#1b2e4b',
                color: '#fff',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Usuário';
        window.location.reload();
    });
}

function confirmarExclusaoUsuario(id, nome) {
    Swal.fire({
        title: 'Remover Usuário?',
        text: `Tem certeza que deseja remover "${nome}" da equipe?`,
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
                    const row = document.getElementById('linha-user-' + id);
                    if (row) row.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Removido!',
                        text: res.mensagem,
                        background: '#1b2e4b',
                        color: '#fff',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
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