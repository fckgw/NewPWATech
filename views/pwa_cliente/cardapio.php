<?php
$empresa         = $data['empresa'] ?? null;
$categorias      = $data['categorias'] ?? [];
$produtos        = $data['produtos'] ?? [];
$adicionais      = $data['adicionais'] ?? [];
$bairros         = $data['bairros'] ?? [];
$pagamentos      = $data['pagamentos'] ?? [];
$enderecosSalvos = $data['enderecosSalvos'] ?? [];
$aberta          = $data['estaAberto'] ?? false;

// Helpers de Sessão
$clienteLogado       = $_SESSION['Cliente_ID'] ?? null;
$clienteNome         = $_SESSION['Cliente_Nome'] ?? '';
$clienteTelefone     = $_SESSION['Cliente_Telefone'] ?? '';
$clienteUltimoAcesso = $_SESSION['Cliente_UltimoAcesso'] ?? '';

// Flash Messages
$showWelcome       = $_SESSION['flash_ShowWelcome'] ?? null;
$userNotFound      = $_SESSION['flash_UserNotFound'] ?? null;
$wrongPassword     = $_SESSION['flash_WrongPassword'] ?? null;
$telefoneTentativa = $_SESSION['flash_TelefoneTentativa'] ?? '';
unset($_SESSION['flash_ShowWelcome'], $_SESSION['flash_UserNotFound'], $_SESSION['flash_WrongPassword'], $_SESSION['flash_TelefoneTentativa']);

$tenantSlug = $empresa->slug ?? '';
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        :root { --pwa-green: #34af23; --pwa-dark: #0b1016; --bg-body: #f4f7f6; }
        body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }

        .sidebar-categorias { position: sticky; top: 20px; min-height: 88vh; background: #fff; border-radius: 30px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #fff; }
        .cat-item { padding: 12px 18px; border-radius: 15px; margin-bottom: 6px; color: #555; text-decoration: none; font-weight: 600; transition: 0.3s; display: block; border-left: 4px solid transparent; cursor: pointer; }
        .cat-item:hover, .cat-item.active { background: #f0fdf4; color: var(--pwa-green); border-left-color: var(--pwa-green); }

        .hero-banner { 
            height: 160px; 
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('/uploads/<?= htmlspecialchars($empresa->img_header ?? "header-default.png") ?>') center/cover; 
            border-radius: 0 0 30px 30px; 
        }
        .search-container { position: relative; margin-top: -35px; z-index: 10; padding: 0 15px; }
        .search-input { border-radius: 50px; padding: 18px 25px 18px 55px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.12); font-size: 1rem; width: 100%; }
        .search-icon { position: absolute; left: 35px; top: 18px; color: #aaa; font-size: 1.3rem; }

        .product-card { background: #fff; border-radius: 25px; padding: 20px; margin-bottom: 15px; display: flex; align-items: center; cursor: pointer; transition: 0.3s; border: 1px solid rgba(0,0,0,0.01); height: 130px; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); border-color: var(--pwa-green); }
        .product-img { width: 95px; height: 95px; border-radius: 18px; object-fit: cover; }
        .img-fallback { width: 95px; height: 95px; background: #f8fafc; border-radius: 18px; display: flex; align-items: center; justify-content: center; color: #cbd5e0; border: 1px dashed #e2e8f0; }

        .cart-float { position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 480px; background: var(--pwa-green); color: #fff; border-radius: 22px; padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 15px 40px rgba(52, 175, 35, 0.45); z-index: 1050; text-decoration: none; display: none; }
        .checkout-layer { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; z-index: 2000; overflow-y: auto; }

        .modal-content { border-radius: 35px !important; border: none; }
        .modal-logo { width: 90px; height: 90px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: -65px; background: white; object-fit: cover; }
        .btn-qty { width: 35px; height: 35px; border-radius: 50%; border: 1px solid #eee; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; cursor: pointer; }

        @media (max-width: 768px) { 
            .sidebar-categorias { display: none; } 
            .product-card { height: auto; } 
        }
    </style>
</head>
<body>

    <textarea id="json-adicionais" style="display:none;"><?= json_encode($adicionais, JSON_UNESCAPED_UNICODE) ?></textarea>
    <input type="hidden" id="nome-loja" value="<?= htmlspecialchars($empresa->nome_empresa ?? '') ?>" />

    <div id="view-home">
        <?php if (!$aberta): ?>
            <div class="alert alert-danger text-center mb-0 border-0 rounded-0 py-3 fw-bold animate__animated animate__fadeInDown">
                <i class="fas fa-clock me-2"></i> ESTAMOS FECHADOS NO MOMENTO
            </div>
        <?php endif; ?>

        <div class="hero-banner"></div>

        <div class="container search-container">
            <div class="search-icon"><i class="fas fa-search"></i></div>
            <input type="text" id="inputBusca" class="form-control search-input" placeholder="O que você deseja comer hoje?" onkeyup="filtrarCatalogo()">
        </div>

        <div class="container mt-4">
            <div class="row">
                <aside class="col-md-3">
                    <div class="sidebar-categorias shadow-sm">
                        <div>
                            <div class="text-center mb-4 p-3 rounded-4 bg-light border">
                                <?php if (!$clienteLogado): ?>
                                    <div class="mb-3"><i class="fas fa-user-circle fa-4x text-muted opacity-25"></i></div>
                                    <button class="btn btn-success btn-sm w-100 rounded-pill fw-bold mb-2 shadow-sm" onclick="abrirModal('modalLogin')">ENTRAR</button>
                                    <button class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold" onclick="abrirModal('modalCadastro')">CRIAR CONTA</button>
                                <?php else: ?>
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow" style="width: 60px; height: 60px; font-size: 1.4rem; font-weight: 800;">
                                        <?= strtoupper(mb_substr($clienteNome, 0, 1)) ?>
                                    </div>
                                    <h6 class="fw-bold mb-0 small"><?= htmlspecialchars($clienteNome) ?></h6>
                                    <a href="/<?= $tenantSlug ?>/cliente/logout" class="text-danger small text-decoration-none fw-bold">Sair da conta</a>
                                <?php endif; ?>
                            </div>
                            <nav id="nav-lista">
                                <?php foreach ($categorias as $cat): ?>
                                    <a href="javascript:void(0)" onclick="scrollToCategoria('sessao-<?= $cat->id ?>')" class="cat-item" id="link-cat-<?= $cat->id ?>">
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
                                ?>
                                    <div class="col-12 col-lg-6 card-produto-wrap" data-nome="<?= strtolower(htmlspecialchars($p->nome_item)) ?>">
                                        <div class="product-card shadow-sm"
                                             data-id="<?= $p->id ?>"
                                             data-nome="<?= htmlspecialchars($p->nome_item) ?>"
                                             data-preco="<?= number_format($p->preco_item, 2, '.', '') ?>"
                                             data-img="<?= htmlspecialchars($p->img_item ?? '') ?>"
                                             data-cat="<?= $p->id_cat ?>"
                                             onclick="abrirFluxoPedido(this)">
                                            <div class="flex-grow-1 pe-2">
                                                <h6 class="fw-bold"><?= htmlspecialchars($p->nome_item) ?></h6>
                                                <p class="text-muted small mb-2"><?= htmlspecialchars($p->descricao_item ?? '') ?></p>
                                                <span class="text-success fw-bold">R$ <?= number_format($p->preco_item, 2, ',', '.') ?></span>
                                            </div>
                                            <?php if (!empty($p->img_item) && $p->img_item !== "false"): ?>
                                                <img src="/uploads/<?= htmlspecialchars($p->img_item) ?>" class="product-img shadow-sm" onerror="this.src='/img/burger.png'">
                                            <?php else: ?>
                                                <div class="img-fallback"><i class="fas fa-hamburger fa-2x"></i></div>
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
            <div class="d-flex align-items-center"><div class="bg-white text-success rounded-3 px-2 py-1 me-3 fw-bold" id="cart-qtd">0</div><b>Ver meu pedido</b></div>
            <div class="fw-bold fs-5" id="cart-total-bar">R$ 0,00</div>
        </a>
    </div>

    <!-- MODAL LOGIN -->
    <div class="modal fade" id="modalLogin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-5 text-center">
                    <img src="/uploads/<?= htmlspecialchars($empresa->img_logo ?? "logo-default.png") ?>" class="modal-logo shadow-sm">
                    <h4 class="fw-bold mb-1">Acesse sua conta</h4>
                    <p class="text-muted small mb-4">Entre no <?= htmlspecialchars($empresa->nome_empresa ?? '') ?></p>

                    <form action="/<?= $tenantSlug ?>/cliente/login" method="POST">
                        <div class="text-start mb-3">
                            <label class="small fw-bold text-muted ms-2">WHATSAPP (LOGIN)</label>
                            <input type="tel" name="telefone" id="login-tel" class="form-control rounded-pill bg-light border-0 py-3 px-4 shadow-sm" placeholder="Seu número" required />
                        </div>
                        <div class="text-start mb-4">
                            <label class="small fw-bold text-muted ms-2">SENHA</label>
                            <div class="input-group bg-light rounded-pill px-2 border-0 shadow-sm">
                                <input type="password" name="senha" id="passLog" class="form-control border-0 bg-transparent py-3 shadow-none" required />
                                <span class="input-group-text border-0 bg-transparent pe-3" onclick="togglePass('passLog', 'eyeLog')">
                                    <i class="fas fa-eye text-muted" id="eyeLog" style="cursor:pointer"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg">ENTRAR</button>
                    </form>
                    <div class="mt-4 small">Esqueceu a senha? <a href="javascript:void(0)" onclick="abrirModal('modalNaoEncontrado')" class="text-success fw-bold">Resetar para o padrão</a></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAIS RESTANTES (CADASTRO, PRODUTO, ETC) -->
    <div class="modal fade" id="modalCadastro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg p-5">
                <div class="text-center mb-4"><h4 class="fw-bold">Cadastro Rápido</h4></div>
                <form action="/<?= $tenantSlug ?>/cliente/registrar" method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">NOME COMPLETO *</label>
                        <input type="text" name="nome_completo" class="form-control rounded-4 border-0 bg-light py-2 shadow-sm" required />
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7 position-relative">
                            <label class="small fw-bold text-muted">WHATSAPP *</label>
                            <input type="tel" name="telefone" id="reg-tel" class="form-control rounded-4 border-0 bg-light py-2 shadow-sm" onkeyup="validarWhatsApp(this)" required />
                        </div>
                        <div class="col-5">
                            <label class="small fw-bold text-muted">NASCIMENTO *</label>
                            <input type="date" name="data_nascimento" class="form-control rounded-4 border-0 bg-light py-2 shadow-sm" required />
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted">E-MAIL (OPCIONAL)</label>
                        <input type="email" name="email" class="form-control rounded-4 border-0 bg-light py-2 shadow-sm" />
                    </div>
                    <p class="small text-muted text-center mb-4">Sua senha padrão será: <br /><b>DDD + Telefone + Ano de Nascimento</b></p>
                    <button type="submit" id="btnReg" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg" disabled>CADASTRAR E PEDIR</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPTS JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        var todosAdicionais = JSON.parse(document.getElementById("json-adicionais").value || "[]");
        var carrinho = [];
        var itemTemp = null;

        $(document).ready(function() {
            $('#reg-tel, #login-tel, #f-tel').mask('(00) 0 0000-0000');
            <?php if ($showWelcome): ?> abrirModal('modalBemVindo'); <?php endif; ?>
            <?php if ($userNotFound): ?> abrirModal('modalNaoEncontrado'); <?php endif; ?>
            <?php if ($wrongPassword): ?> abrirModal('modalLogin'); <?php endif; ?>
        });

        function abrirModal(id) {
            document.querySelectorAll('.modal').forEach(m => { 
                const inst = bootstrap.Modal.getInstance(m); 
                if (inst) inst.hide(); 
            });
            var mEl = document.getElementById(id);
            if (mEl) { new bootstrap.Modal(mEl).show(); }
        }

        function scrollToCategoria(id) {
            var el = document.getElementById(id);
            if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: "smooth" });
        }

        function filtrarCatalogo() {
            var t = $("#inputBusca").val().toLowerCase().trim();
            if (t === "") { $(".card-produto-wrap, .categoria-bloco").show(); return; }
            $(".card-produto-wrap").each(function() { $(this).toggle($(this).attr("data-nome").includes(t)); });
            $(".categoria-bloco").each(function() { $(this).toggle($(this).find(".card-produto-wrap:visible").length > 0); });
        }

        function togglePass(id, eyeId) {
            var x = document.getElementById(id); var i = document.getElementById(eyeId);
            if (x.type === "password") { x.type = "text"; i.classList.replace("fa-eye", "fa-eye-slash"); }
            else { x.type = "password"; i.classList.replace("fa-eye-slash", "fa-eye"); }
        }

        function validarWhatsApp(el) {
            var val = el.value.replace(/\D/g, '');
            $("#btnReg").prop('disabled', val.length < 11);
        }
    </script>
</body>
</html>