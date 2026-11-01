<?php
$empresa         = $data['empresa'] ?? null;
$categorias      = $data['categorias'] ?? [];
$produtos        = $data['produtos'] ?? [];
$adicionais      = $data['adicionais'] ?? [];
$bairros         = $data['bairros'] ?? [];
$pagamentos      = $data['pagamentos'] ?? [];
$enderecosSalvos = $data['enderecosSalvos'] ?? [];
$aberta          = $data['estaAberto'] ?? false;

// Sessão do Cliente
$clienteLogado       = $_SESSION['Cliente_ID'] ?? null;
$clienteNome         = $_SESSION['Cliente_Nome'] ?? '';
$clienteTelefone     = $_SESSION['Cliente_Telefone'] ?? '';
$clienteUltimoAcesso = $_SESSION['Cliente_UltimoAcesso'] ?? '';

// Flash Messages
$showWelcome       = $_SESSION['flash_ShowWelcome'] ?? null;
$userNotFound      = $_SESSION['flash_UserNotFound'] ?? null;
$wrongPassword     = $_SESSION['flash_WrongPassword'] ?? null;
$telefoneTentativa = $_SESSION['flash_TelefoneTentativa'] ?? '';
$erroCadastro      = $_SESSION['flash_ErroCadastro'] ?? '';

unset(
    $_SESSION['flash_ShowWelcome'],
    $_SESSION['flash_UserNotFound'],
    $_SESSION['flash_WrongPassword'],
    $_SESSION['flash_TelefoneTentativa'],
    $_SESSION['flash_ErroCadastro']
);

$slug = $empresa->nome_empresa_link ?? 'loja';

// Tratamento de Imagem de Header/Capa e Logo com Fallback
$headerImg = (!empty($empresa->img_header) && $empresa->img_header !== 'header-default.png' && file_exists(__DIR__ . '/../../uploads/' . $empresa->img_header))
    ? '/uploads/' . htmlspecialchars($empresa->img_header)
    : 'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=1200&q=80';

$logoImg = (!empty($empresa->img_logo) && $empresa->img_logo !== 'logo-default.png' && file_exists(__DIR__ . '/../../uploads/' . $empresa->img_logo))
    ? '/uploads/' . htmlspecialchars($empresa->img_logo)
    : 'https://cdn-icons-png.flaticon.com/512/3075/3075977.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= htmlspecialchars($empresa->nome_empresa ?? 'Cardápio') ?> | Cardápio Digital</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root { --pwa-green: #34af23; --pwa-dark: #0b1016; --bg-body: #f4f7f6; }
        body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }

        .hero-banner { 
            height: 180px; 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url('<?= $headerImg ?>') center/cover no-repeat; 
            border-radius: 0 0 30px 30px; 
            position: relative;
        }
        .store-badge {
            position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%);
            width: 80px; height: 80px; border-radius: 50%; border: 4px solid #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15); object-fit: cover; background: #fff;
        }

        .sidebar-categorias { position: sticky; top: 20px; min-height: 80vh; background: #fff; border-radius: 25px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04); }
        .cat-item { padding: 12px 18px; border-radius: 14px; margin-bottom: 6px; color: #555; text-decoration: none; font-weight: 600; transition: 0.2s; display: block; border-left: 4px solid transparent; cursor: pointer; }
        .cat-item:hover, .cat-item.active { background: #f0fdf4; color: var(--pwa-green); border-left-color: var(--pwa-green); }

        .search-container { position: relative; margin-top: 45px; z-index: 10; padding: 0 15px; }
        .search-input { border-radius: 50px; padding: 16px 25px 16px 50px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); font-size: 0.95rem; width: 100%; }
        .search-icon { position: absolute; left: 32px; top: 16px; color: #aaa; }

        .product-card { background: #fff; border-radius: 22px; padding: 18px; margin-bottom: 15px; display: flex; align-items: center; cursor: pointer; transition: 0.2s; border: 1px solid rgba(0,0,0,0.03); min-height: 125px; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); border-color: var(--pwa-green); }
        .product-img { width: 90px; height: 90px; border-radius: 16px; object-fit: cover; }
        .img-fallback { width: 90px; height: 90px; background: #f8fafc; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #cbd5e0; border: 1px dashed #e2e8f0; font-size: 1.8rem; }

        .cart-float { position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 460px; background: var(--pwa-green); color: #fff; border-radius: 20px; padding: 16px 22px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 15px 35px rgba(52, 175, 35, 0.45); z-index: 1050; text-decoration: none; display: none; }
        .checkout-layer { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; z-index: 2000; overflow-y: auto; }

        .btn-qty { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #ddd; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; cursor: pointer; }
        .input-qty-main { width: 65px; height: 42px; text-align: center; font-weight: 800; font-size: 1.1rem; border-radius: 12px; border: 1px solid #dee2e6; }
    </style>
</head>
<body>

    <textarea id="json-adicionais" style="display:none;"><?= json_encode($adicionais, JSON_UNESCAPED_UNICODE) ?></textarea>

    <div id="view-home">
        <?php if (!$aberta): ?>
            <div class="alert alert-danger text-center mb-0 border-0 rounded-0 py-3 fw-bold animate__animated animate__fadeInDown">
                <i class="fas fa-clock me-2"></i> ESTAMOS FECHADOS NO MOMENTO
            </div>
        <?php endif; ?>

        <div class="hero-banner">
            <img src="<?= $logoImg ?>" class="store-badge" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3075/3075977.png'">
        </div>

        <div class="text-center mt-4 pt-3">
            <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($empresa->nome_empresa ?? 'Minha Loja') ?></h4>
            <p class="text-muted small mb-0"><?= htmlspecialchars($empresa->descricao_empresa ?? 'Bem-vindo ao nosso cardápio online!') ?></p>
        </div>

        <div class="container search-container">
            <div class="search-icon"><i class="fas fa-search"></i></div>
            <input type="text" id="inputBusca" class="form-control search-input" placeholder="O que você deseja pedir hoje?" onkeyup="filtrarCatalogo()">
        </div>

        <div class="container mt-4">
            <div class="row">
                <aside class="col-md-3">
                    <div class="sidebar-categorias">
                        <div>
                            <div class="text-center mb-4 p-3 rounded-4 bg-light border">
                                <?php if (!$clienteLogado): ?>
                                    <div class="mb-2"><i class="fas fa-user-circle fa-3x text-muted opacity-50"></i></div>
                                    <button class="btn btn-success btn-sm w-100 rounded-pill fw-bold mb-2 shadow-sm py-2" onclick="abrirModal('modalLogin')">ENTRAR</button>
                                    <button class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold py-2" onclick="abrirModal('modalCadastro')">CRIAR CONTA</button>
                                <?php else: ?>
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 55px; height: 55px; font-size: 1.3rem; font-weight: 800;">
                                        <?= strtoupper(mb_substr($clienteNome, 0, 1)) ?>
                                    </div>
                                    <h6 class="fw-bold mb-0 small text-dark"><?= htmlspecialchars($clienteNome) ?></h6>
                                    <a href="/<?= $slug ?>?acao_cliente=logout" class="text-danger small text-decoration-none fw-bold d-block mt-1">Sair da conta</a>
                                <?php endif; ?>
                            </div>

                            <nav id="nav-lista">
                                <?php foreach ($categorias as $cat): ?>
                                    <a href="javascript:void(0)" onclick="scrollToCategoria('sessao-<?= $cat->id ?>')" class="cat-item">
                                        <?= htmlspecialchars($cat->nome_cat) ?>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                    </div>
                </aside>

                <main class="col-md-9 pb-5">
                    <?php foreach ($categorias as $cat): ?>
                        <div id="sessao-<?= $cat->id ?>" class="categoria-bloco mb-5">
                            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2"><?= htmlspecialchars($cat->nome_cat) ?></h5>
                            <div class="row g-3">
                                <?php 
                                foreach ($produtos as $p): 
                                    if ($p->id_cat != $cat->id) continue;
                                    $imgItem = (!empty($p->img_item) && $p->img_item !== 'false' && file_exists(__DIR__ . '/../../uploads/' . $p->img_item)) 
                                        ? '/uploads/' . htmlspecialchars($p->img_item) 
                                        : '';
                                ?>
                                    <div class="col-12 col-lg-6 card-produto-wrap" data-nome="<?= strtolower(htmlspecialchars($p->nome_item)) ?>">
                                        <div class="product-card shadow-sm"
                                             data-id="<?= $p->id ?>"
                                             data-nome="<?= htmlspecialchars($p->nome_item) ?>"
                                             data-preco="<?= number_format($p->preco_item, 2, '.', '') ?>"
                                             data-img="<?= htmlspecialchars($imgItem) ?>"
                                             data-cat="<?= $p->id_cat ?>"
                                             onclick="abrirFluxoPedido(this)">
                                            <div class="flex-grow-1 pe-2">
                                                <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($p->nome_item) ?></h6>
                                                <p class="text-muted small mb-2"><?= htmlspecialchars($p->descricao_item ?? '') ?></p>
                                                <span class="text-success fw-bold">R$ <?= number_format($p->preco_item, 2, ',', '.') ?></span>
                                            </div>
                                            <?php if ($imgItem): ?>
                                                <img src="<?= $imgItem ?>" class="product-img shadow-sm" onerror="this.style.display='none'">
                                            <?php else: ?>
                                                <div class="img-fallback"><i class="fas fa-hamburger text-secondary opacity-50"></i></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </main>
            </div>
        </div>

        <a href="javascript:void(0)" class="cart-float animate__animated" id="barra-carrinho" onclick="abrirCheckout()">
            <div class="d-flex align-items-center">
                <div class="bg-white text-success rounded-3 px-2 py-1 me-3 fw-bold" id="cart-qtd">0</div>
                <b>Ver meu pedido</b>
            </div>
            <div class="fw-bold fs-5" id="cart-total-bar">R$ 0,00</div>
        </a>
    </div>

    <!-- MODAL LOGIN -->
    <div class="modal fade" id="modalLogin" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg p-4 text-center">
                <h4 class="fw-bold mb-1">Acessar Conta</h4>
                <p class="text-muted small mb-4">Entre para agilizar seus pedidos</p>

                <form action="/<?= $slug ?>?acao_cliente=login" method="POST">
                    <div class="text-start mb-3">
                        <label class="small fw-bold text-muted mb-1">WHATSAPP</label>
                        <input type="tel" name="telefone" id="login-tel" class="form-control rounded-pill bg-light border-0 py-3 px-4 shadow-sm" placeholder="(00) 0 0000-0000" required />
                    </div>
                    <div class="text-start mb-4">
                        <label class="small fw-bold text-muted mb-1">SENHA</label>
                        <input type="password" name="senha" class="form-control rounded-pill bg-light border-0 py-3 px-4 shadow-sm" placeholder="••••••••" required />
                        <small class="text-muted d-block mt-2 text-center" style="font-size: 11px;">Senha padrão: DDD + Telefone + Ano de Nascimento</small>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow">ENTRAR NO CARDÁPIO</button>
                </form>
                <div class="mt-3 small">
                    Não tem conta? <a href="javascript:void(0)" onclick="abrirModal('modalCadastro')" class="text-success fw-bold">Criar Cadastro</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CADASTRO -->
    <div class="modal fade" id="modalCadastro" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg p-4">
                <div class="text-center mb-3">
                    <h4 class="fw-bold mb-1">Cadastro Rápido</h4>
                    <p class="text-muted small">Preencha seus dados para pedir</p>
                </div>

                <?php if (!empty($erroCadastro)): ?>
                    <div class="alert alert-danger py-2 small mb-3 rounded-3 text-center">
                        <i class="fas fa-exclamation-triangle me-1"></i> <?= htmlspecialchars($erroCadastro) ?>
                    </div>
                <?php endif; ?>

                <form action="/<?= $slug ?>?acao_cliente=registrar" method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">NOME COMPLETO *</label>
                        <input type="text" name="nome_completo" class="form-control rounded-4 border-0 bg-light py-2 px-3 shadow-sm" placeholder="Seu nome" required />
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="small fw-bold text-muted mb-1">WHATSAPP *</label>
                            <input type="tel" name="telefone" id="reg-tel" class="form-control rounded-4 border-0 bg-light py-2 px-3 shadow-sm" placeholder="(00) 0 0000-0000" onkeyup="validarWhatsApp(this)" required />
                        </div>
                        <div class="col-5">
                            <label class="small fw-bold text-muted mb-1">NASCIMENTO *</label>
                            <input type="date" name="data_nascimento" class="form-control rounded-4 border-0 bg-light py-2 px-2 shadow-sm" required />
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-1">E-MAIL (OPCIONAL)</label>
                        <input type="email" name="email" class="form-control rounded-4 border-0 bg-light py-2 px-3 shadow-sm" placeholder="seu@email.com" />
                    </div>
                    <p class="small text-muted text-center mb-3" style="font-size: 11px;">
                        Sua senha padrão será gerada automaticamente:<br>
                        <b>DDD + Telefone + Ano de Nascimento</b> (sem traços)
                    </p>
                    <button type="submit" id="btnReg" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow" disabled>CADASTRAR E PEDIR</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PRODUTO, QUANTIDADE & ADICIONAIS -->
    <div class="modal fade" id="modalFluxo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                    <h5 class="fw-bold mb-0 text-dark" id="m-nome">Detalhes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <div id="step-produto">
                        <img id="m-img" src="" class="w-100 rounded-4 mb-3 shadow-sm" style="height: 180px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=80'">

                        <!-- SELETOR DE QUANTIDADE DO LANCHE (ATÉ 3 DÍGITOS / 999) -->
                        <div class="bg-light p-3 rounded-4 mb-3 border d-flex justify-content-between align-items-center">
                            <div class="text-start">
                                <b class="text-dark d-block">Quantidade:</b>
                                <small class="text-muted">Quantos lanches deseja?</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn-qty" onclick="alterarQtdItem(-1)">-</button>
                                <input type="number" id="m-qtd-item" class="input-qty-main" value="1" min="1" max="999" maxlength="3" oninput="validarQtdDigitada(this)">
                                <button type="button" class="btn-qty text-success" onclick="alterarQtdItem(1)">+</button>
                            </div>
                        </div>

                        <!-- PERGUNTA SOBRE ADICIONAIS -->
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 mb-3 border border-success border-opacity-10">
                            <h6 class="fw-bold text-success mb-2">Deseja adicionar itens extras?</h6>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold btn-sm" onclick="irParaAdicionais()">SIM</button>
                                <button type="button" class="btn btn-light rounded-pill px-4 border btn-sm" onclick="finalizarSelecao()">NÃO</button>
                            </div>
                        </div>
                    </div>

                    <div id="step-adicionais" style="display:none;" class="text-start">
                        <div id="lista-adicionais-dinamica" style="max-height: 220px; overflow-y: auto;"></div>
                        <button type="button" class="btn btn-light btn-sm w-100 mt-2 rounded-pill border fw-bold" onclick="voltarParaProduto()">Voltar ao Item</button>
                    </div>

                    <textarea id="m-obs" class="form-control mt-3 rounded-3 bg-light border-0" rows="2" placeholder="Ex: Sem cebola, maionese à parte..."></textarea>
                </div>
                <div class="p-2 pt-0">
                    <button type="button" class="btn btn-success w-100 rounded-pill fw-bold py-3 fs-6 shadow" onclick="finalizarSelecao()">
                        CONFIRMAR - <span id="m-total-btn">R$ 0,00</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CHECKOUT FULLSCREEN -->
    <div id="view-checkout" class="checkout-layer p-4">
        <div class="container" style="max-width: 600px;">
            <div class="d-flex align-items-center mb-4">
                <button type="button" class="btn btn-light rounded-circle border me-3" onclick="fecharCheckout()"><i class="fas fa-arrow-left"></i></button>
                <h4 class="fw-bold mb-0">Finalizar Pedido</h4>
            </div>

            <div id="carrinho-itens-lista" class="mb-4"></div>

            <div class="card border-0 shadow-sm p-4 rounded-4 bg-light mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-map-marker-alt text-success me-2"></i>Entrega</h6>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">SEU NOME</label>
                    <input type="text" id="f-nome" class="form-control rounded-3" value="<?= htmlspecialchars($clienteNome) ?>">
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">WHATSAPP</label>
                    <input type="tel" id="f-tel" class="form-control rounded-3" value="<?= htmlspecialchars($clienteTelefone) ?>">
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">BAIRRO</label>
                    <select id="f-bairro" class="form-select rounded-3" onchange="atualizarTotalFinal()">
                        <option value="0" data-taxa="0">Selecione o Bairro...</option>
                        <?php foreach ($bairros as $b): ?>
                            <option value="<?= $b->id ?>" data-taxa="<?= number_format($b->taxa, 2, '.', '') ?>">
                                <?= htmlspecialchars($b->bairro) ?> (R$ <?= number_format($b->taxa, 2, ',', '.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8"><label class="small fw-bold text-muted">RUA</label><input type="text" id="f-rua" class="form-control rounded-3"></div>
                    <div class="col-4"><label class="small fw-bold text-muted">NÚMERO</label><input type="text" id="f-num" class="form-control rounded-3"></div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">FORMA DE PAGAMENTO</label>
                    <select id="f-pgto" class="form-select rounded-3">
                        <?php foreach ($pagamentos as $pg): ?>
                            <option value="<?= htmlspecialchars($pg->f_pagamento) ?>"><?= htmlspecialchars($pg->f_pagamento) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="bg-dark text-white p-4 rounded-4 mb-4 shadow-lg">
                <div class="d-flex justify-content-between mb-2 small text-secondary"><span>Subtotal:</span> <span id="f-subtotal">R$ 0,00</span></div>
                <div class="d-flex justify-content-between mb-2 small text-secondary"><span>Taxa de Entrega:</span> <span id="f-taxa">R$ 0,00</span></div>
                <div class="d-flex justify-content-between fw-bold fs-4 border-top border-secondary pt-2 mt-2">
                    <span>Total:</span> <span class="text-success" id="f-total">R$ 0,00</span>
                </div>
            </div>

            <button type="button" id="btnConcluirPedido" class="btn btn-success w-100 rounded-pill py-3 fw-bold fs-5 shadow-lg mb-5" onclick="enviarPedidoFinal()">
                <i class="fas fa-check-circle me-2"></i> CONCLUIR O PEDIDO
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var todosAdicionais = JSON.parse(document.getElementById("json-adicionais").value || "[]");
        var carrinho = [];
        var itemTemp = null;

        $(document).ready(function() {
            $('#login-tel, #reg-tel, #f-tel').mask('(00) 0 0000-0000');

            <?php if ($showWelcome): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Bem-vindo, <?= addslashes($clienteNome) ?>!',
                    text: 'Que bom ter você de volta no cardápio.',
                    confirmButtonColor: '#34af23'
                });
            <?php endif; ?>

            <?php if ($userNotFound): ?>
                abrirModal('modalCadastro');
                document.getElementById('reg-tel').value = '<?= htmlspecialchars($telefoneTentativa) ?>';
            <?php endif; ?>

            <?php if ($wrongPassword): ?>
                abrirModal('modalLogin');
                Swal.fire({ icon: 'error', title: 'Senha Incorreta', text: 'Verifique a senha informada.' });
            <?php endif; ?>

            <?php if (!empty($erroCadastro)): ?>
                abrirModal('modalCadastro');
            <?php endif; ?>
        });

        function abrirModal(id) {
            document.querySelectorAll('.modal').forEach(m => {
                const inst = bootstrap.Modal.getInstance(m);
                if (inst) inst.hide();
            });
            var mEl = document.getElementById(id);
            if (mEl) new bootstrap.Modal(mEl).show();
        }

        function scrollToCategoria(id) {
            var el = document.getElementById(id);
            if (el) window.scrollTo({ top: el.offsetTop - 70, behavior: "smooth" });
        }

        function filtrarCatalogo() {
            var t = $("#inputBusca").val().toLowerCase().trim();
            if (t === "") { $(".card-produto-wrap, .categoria-bloco").show(); return; }
            $(".card-produto-wrap").each(function() { $(this).toggle($(this).attr("data-nome").includes(t)); });
            $(".categoria-bloco").each(function() { $(this).toggle($(this).find(".card-produto-wrap:visible").length > 0); });
        }

        function validarWhatsApp(el) {
            var val = el.value.replace(/\D/g, '');
            document.getElementById('btnReg').disabled = (val.length < 11);
        }

        function abrirFluxoPedido(el) {
            var d = $(el).data();
            itemTemp = { 
                id: d.id, 
                nome: d.nome, 
                precoUnitario: parseFloat(d.preco), 
                quantidade: 1, 
                idCat: d.cat, 
                adicionais: [], 
                obs: "", 
                totalItem: parseFloat(d.preco) 
            };
            
            $("#m-nome").text(d.nome);
            $("#m-img").attr("src", d.img || "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=80");
            $("#m-qtd-item").val(1);
            recalcularTotalItemModal();

            $("#step-produto").show(); 
            $("#step-adicionais").hide(); 
            $("#m-obs").val("");

            var h = "";
            var f = todosAdicionais.filter(a => String(a.categorias_adicional || '').split(',').map(s => s.trim()).includes(String(d.cat)));
            f.forEach(a => {
                var adId = a.Id || a.id;
                var adNome = a.nome_adicional || a.nome;
                var adValor = parseFloat(a.valor_adicional || 0);

                h += `<div class="d-flex justify-content-between align-items-center mb-2 bg-light p-2 rounded-3">
                    <div><b>${adNome}</b><br><small class="text-success">+ R$ ${adValor.toLocaleString('pt-BR',{minimumFractionDigits:2})}</small></div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-qty" onclick="qtdAd(${adId}, -1, ${adValor}, '${adNome}')">-</button>
                        <span id="q-val-${adId}" class="fw-bold">0</span>
                        <button type="button" class="btn-qty text-success" onclick="qtdAd(${adId}, 1, ${adValor}, '${adNome}')">+</button>
                    </div></div>`;
            });
            $("#lista-adicionais-dinamica").html(h || "<p class='small text-muted py-2'>Sem adicionais para este item.</p>");
            abrirModal('modalFluxo');
        }

        function alterarQtdItem(delta) {
            var input = document.getElementById('m-qtd-item');
            var val = parseInt(input.value) || 1;
            val += delta;
            if (val < 1) val = 1;
            if (val > 999) val = 999;
            input.value = val;
            itemTemp.quantidade = val;
            recalcularTotalItemModal();
        }

        function validarQtdDigitada(input) {
            if (input.value.length > 3) {
                input.value = input.value.slice(0, 3);
            }
            var val = parseInt(input.value) || 1;
            if (val < 1) val = 1;
            if (val > 999) val = 999;
            itemTemp.quantidade = val;
            recalcularTotalItemModal();
        }

        function recalcularTotalItemModal() {
            var subUnitario = itemTemp.precoUnitario;
            itemTemp.adicionais.forEach(ad => {
                subUnitario += (ad.valor * ad.qtd);
            });
            itemTemp.totalItem = subUnitario * itemTemp.quantidade;
            $("#m-total-btn").text("R$ " + itemTemp.totalItem.toLocaleString('pt-BR', {minimumFractionDigits: 2}));
        }

        function irParaAdicionais() { $("#step-produto").hide(); $("#step-adicionais").fadeIn(); }
        function voltarParaProduto() { $("#step-adicionais").hide(); $("#step-produto").fadeIn(); }

        function qtdAd(id, a, v, n) {
            var e = $("#q-val-" + id); 
            var q = parseInt(e.text()) + a; 
            if (q < 0) return; 
            e.text(q);

            var x = itemTemp.adicionais.findIndex(i => i.id === id);
            if (q > 0) {
                if (x > -1) itemTemp.adicionais[x].qtd = q;
                else itemTemp.adicionais.push({id: id, nome: n, valor: v, qtd: q});
            } else { 
                itemTemp.adicionais.splice(x, 1); 
            }

            recalcularTotalItemModal();
        }

        function finalizarSelecao() {
            itemTemp.obs = $("#m-obs").val();
            carrinho.push(JSON.parse(JSON.stringify(itemTemp)));
            document.querySelectorAll('.modal').forEach(m => bootstrap.Modal.getInstance(m)?.hide());
            atualizarBarraCarrinho();
        }

        function atualizarBarraCarrinho() {
            if (carrinho.length > 0) {
                $("#barra-carrinho").show().addClass("animate__slideInUp");
                var totalGeral = carrinho.reduce((sum, i) => sum + i.totalItem, 0);
                var qtdTotalItens = carrinho.reduce((sum, i) => sum + i.quantidade, 0);
                $("#cart-qtd").text(qtdTotalItens);
                $("#cart-total-bar").text("R$ " + totalGeral.toLocaleString('pt-BR', {minimumFractionDigits:2}));
            }
        }

        function abrirCheckout() {
            $("#view-home").hide();
            $("#view-checkout").fadeIn();
            var listaHtml = "";
            carrinho.forEach(item => {
                var adTxt = item.adicionais.map(a => `${a.qtd}x ${a.nome}`).join(', ');
                listaHtml += `<div class="d-flex justify-content-between border-bottom py-2">
                    <div>
                        <b>${item.quantidade}x ${item.nome}</b><br>
                        <small class="text-muted">${adTxt ? '+' + adTxt : ''} ${item.obs ? '(' + item.obs + ')' : ''}</small>
                    </div>
                    <div><b>R$ ${item.totalItem.toLocaleString('pt-BR',{minimumFractionDigits:2})}</b></div>
                </div>`;
            });
            $("#carrinho-itens-lista").html(listaHtml);
            atualizarTotalFinal();
        }

        function fecharCheckout() { $("#view-checkout").hide(); $("#view-home").fadeIn(); }

        function atualizarTotalFinal() {
            var subtotal = carrinho.reduce((sum, i) => sum + i.totalItem, 0);
            var taxa = parseFloat($("#f-bairro option:selected").data("taxa") || 0);
            var total = subtotal + taxa;
            $("#f-subtotal").text("R$ " + subtotal.toLocaleString('pt-BR', {minimumFractionDigits:2}));
            $("#f-taxa").text("R$ " + taxa.toLocaleString('pt-BR', {minimumFractionDigits:2}));
            $("#f-total").text("R$ " + total.toLocaleString('pt-BR', {minimumFractionDigits:2}));
        }

        function enviarPedidoFinal() {
            var btn = document.getElementById("btnConcluirPedido");
            
            // Remove o foco do botão antes do SweetAlert2 (Elimina o aviso de aria-hidden)
            if (btn) btn.blur();
            if (document.activeElement) document.activeElement.blur();

            var nome   = $("#f-nome").val().trim();
            var tel    = $("#f-tel").val().trim();
            var rua    = $("#f-rua").val().trim();
            var num    = $("#f-num").val().trim();
            var bId    = $("#f-bairro").val();
            var bNome  = $("#f-bairro option:selected").text().trim();
            var taxa   = parseFloat($("#f-bairro option:selected").data("taxa") || 0);
            var pgto   = $("#f-pgto").val();

            if (!nome || !tel || !rua || bId === "0") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos Obrigatórios',
                    text: 'Por favor, informe seu Nome, WhatsApp, Bairro e Rua para a entrega.',
                    confirmButtonColor: '#34af23'
                });
                return;
            }

            if (carrinho.length === 0) {
                Swal.fire({ 
                    icon: 'info', 
                    title: 'Carrinho Vazio', 
                    text: 'Selecione ao menos um produto antes de concluir o pedido!',
                    confirmButtonColor: '#34af23'
                });
                return;
            }

            var subtotal = carrinho.reduce((sum, i) => sum + i.totalItem, 0);
            var total = subtotal + taxa;

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> PROCESSANDO PEDIDO...';
            }

            Swal.fire({
                title: 'Gravando Pedido...',
                text: 'Enviando seu pedido diretamente para a cozinha.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            var payload = {
                nome: nome,
                telefone: tel,
                rua: rua,
                numero: num,
                bairro_id: bId,
                bairro_nome: bNome,
                forma_pagamento: pgto,
                taxa_entrega: taxa,
                subtotal: subtotal,
                total: total,
                itens: carrinho
            };

            fetch('/<?= $slug ?>?acao_cliente=salvar_pedido', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.text())
            .then(textoRecebido => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> CONCLUIR O PEDIDO';
                }

                var res;
                try {
                    res = JSON.parse(textoRecebido);
                } catch (errJson) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Resposta do Servidor',
                        html: `<div class="text-start small" style="max-height:220px; overflow-y:auto; background:#1b2e4b; color:#fff; padding:10px; border-radius:8px;">${textoRecebido}</div>`,
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                if (res.sucesso) {
                    carrinho = [];
                    atualizarBarraCarrinho();
                    fecharCheckout();

                    // Pop-up Oficial Conforme Solicitado
                    Swal.fire({
                        icon: 'success',
                        title: 'Pedido Confirmado!',
                        html: `<div style="font-size: 1.15rem; line-height: 1.6;">
                                 Pedido número <b>#${res.codigo_pedido}</b> foi gerado com sucesso!<br>
                                 <span class="text-muted" style="font-size: 0.95rem;">Aguarde as atualizações no seu WhatsApp.</span>
                               </div>`,
                        confirmButtonColor: '#34af23',
                        confirmButtonText: 'Entendido, Obrigado!'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao Gravar',
                        text: res.mensagem || 'Ocorreu uma falha ao registrar o pedido no banco.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> CONCLUIR O PEDIDO';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Falha de Comunicação',
                    text: 'Não foi possível se comunicar com o servidor: ' + err.message,
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    </script>
</body>
</html>