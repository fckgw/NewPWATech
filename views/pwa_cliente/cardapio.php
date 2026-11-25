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

// Helper de Resolução de Imagem Blindada contra Erros 404 e Loops
function resolverUrlSegura(?string $arquivo, string $fallbackUrl): string {
    if (empty($arquivo) || $arquivo === 'false' || $arquivo === 'header-default.png' || $arquivo === 'logo-default.png') {
        return $fallbackUrl;
    }
    if (str_starts_with($arquivo, 'http://') || str_starts_with($arquivo, 'https://')) {
        return $arquivo;
    }
    $arquivoLimpo = ltrim($arquivo, '/');
    $caminhoFisico = __DIR__ . '/../../uploads/' . $arquivoLimpo;
    if (file_exists($caminhoFisico)) {
        return '/uploads/' . $arquivoLimpo;
    }
    return $fallbackUrl;
}

$headerImg = resolverUrlSegura(
    $empresa->img_header ?? null,
    'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=1200&q=80'
);

$logoImg = resolverUrlSegura(
    $empresa->img_logo ?? null,
    'https://cdn-icons-png.flaticon.com/512/3075/3075977.png'
);

$enderecoFormatado = trim(($empresa->end_rua_n_empresa ?? 'Av. Brasil, 19') . ' - ' . ($empresa->cidade_empresa ?? 'Caçapava'));
$telefoneFormatado = !empty($empresa->telefone_empresa) ? $empresa->telefone_empresa : '99607-4068';
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-red: #d31018;
            --brand-red-hover: #b90e15;
            --brand-yellow: #ffc700;
            --bg-page: #f8fafc;
            --text-dark: #1e293b;
        }

        body {
            background-color: var(--bg-page);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            padding-bottom: 90px;
        }

        /* HEADER HERO */
        .header-hero {
            position: relative;
            background: linear-gradient(135deg, var(--brand-yellow) 0%, #f59e0b 50%, var(--brand-red) 100%);
            background-image: url('<?= $headerImg ?>');
            background-size: cover;
            background-position: center;
            height: 210px;
            border-bottom-left-radius: 35px;
            border-bottom-right-radius: 35px;
            box-shadow: 0 10px 25px rgba(211, 16, 24, 0.15);
        }
        .header-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6));
            border-bottom-left-radius: 35px;
            border-bottom-right-radius: 35px;
        }

        .btn-top-nav {
            width: 44px; height: 44px;
            background: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            text-decoration: none;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-top-nav:hover { background: #f8fafc; color: var(--brand-red); transform: scale(1.05); }

        .header-content {
            position: absolute;
            bottom: 25px;
            left: 0;
            width: 100%;
            text-align: center;
            color: #fff;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .header-logo-badge {
            width: 72px;
            height: 72px;
            min-width: 72px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            object-fit: cover;
            background: #fff;
        }
        .header-brand-title {
            font-size: 1.85rem;
            font-weight: 900;
            text-shadow: 0 3px 10px rgba(0,0,0,0.7);
            letter-spacing: -0.5px;
            margin-bottom: 2px;
            color: #fff;
            line-height: 1.1;
        }
        .header-slogan {
            font-size: 0.88rem;
            font-style: italic;
            font-weight: 700;
            color: var(--brand-yellow);
            text-shadow: 0 2px 6px rgba(0,0,0,0.7);
            margin: 0;
        }

        /* CARD FLUTUANTE CONTATO */
        .contact-pill-card {
            background: #fff;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 12px 24px;
            margin-top: -24px;
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #334155;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .contact-divider { width: 2px; height: 18px; background: #cbd5e1; }

        /* CARROSSEL DE CATEGORIAS */
        .cat-scroll-wrapper {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            gap: 10px;
            padding: 18px 0 10px 0;
            scrollbar-width: none;
        }
        .cat-scroll-wrapper::-webkit-scrollbar { display: none; }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 9px 20px;
            font-weight: 700;
            font-size: 0.92rem;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 3px 8px rgba(0,0,0,0.02);
            cursor: pointer;
        }
        .cat-pill:hover { color: var(--brand-red); border-color: rgba(211, 16, 24, 0.3); background: #fff; }
        .cat-pill.active {
            background: var(--brand-red) !important;
            color: #fff !important;
            border-color: var(--brand-red) !important;
            box-shadow: 0 6px 18px rgba(211, 16, 24, 0.35);
        }
        .cat-pill.active i { color: #fff !important; }

        .categoria-titulo-bloco { display: flex; justify-content: space-between; align-items: flex-end; margin: 25px 0 15px 0; position: relative; }
        .categoria-nome { font-size: 1.55rem; font-weight: 900; color: #0f172a; margin: 0; position: relative; display: inline-block; }
        .categoria-nome::after { content: ''; position: absolute; left: 0; bottom: -5px; width: 45%; height: 3px; background: var(--brand-red); border-radius: 10px; }
        .ver-mais-link { color: var(--brand-red); font-weight: 700; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }

        /* CARDS PRODUTOS */
        .product-card-pro {
            background: #fff;
            border-radius: 24px;
            padding: 14px 16px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border: 1px solid #edf2f7;
            box-shadow: 0 6px 18px rgba(0,0,0,0.03);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card-pro:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(0,0,0,0.07); border-color: rgba(211, 16, 24, 0.2); }

        .prod-img-box { width: 105px; height: 105px; min-width: 105px; border-radius: 18px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc; }
        .prod-img-box img { width: 100%; height: 100%; object-fit: cover; border-radius: 18px; }
        .prod-img-fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 2rem; border: 1px dashed #e2e8f0; border-radius: 18px; }

        .prod-info { flex-grow: 1; padding-right: 5px; }
        .prod-title { font-weight: 800; font-size: 1.12rem; color: #0f172a; margin-bottom: 4px; }
        .prod-desc { font-size: 0.84rem; color: #64748b; line-height: 1.35; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .prod-price { font-size: 1.25rem; font-weight: 900; color: var(--brand-red); }

        .btn-add-circle {
            width: 44px; height: 44px; min-width: 44px; background: var(--brand-red); color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 6px 15px rgba(211, 16, 24, 0.35);
            font-size: 1.25rem; font-weight: 700; transition: all 0.2s ease;
        }
        .btn-add-circle:hover { background: var(--brand-red-hover); transform: scale(1.08); color: #fff; }

        /* CARRINHO */
        .cart-bar-bottom {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 480px;
            background: #fff; border-radius: 50px; padding: 12px 22px; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 15px 35px rgba(0,0,0,0.18); border: 2px solid var(--brand-red); z-index: 1050; text-decoration: none; cursor: pointer; transition: all 0.3s;
        }
        .cart-icon-wrap { position: relative; display: inline-flex; align-items: center; justify-content: center; }
        .cart-badge-count {
            position: absolute; top: -8px; right: -10px; background: var(--brand-red); color: #fff; font-size: 0.72rem;
            font-weight: 800; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;
        }

        .checkout-layer { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; z-index: 2000; overflow-y: auto; }
        .btn-qty { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #ddd; background: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; cursor: pointer; }
        .input-qty-main { width: 65px; height: 42px; text-align: center; font-weight: 800; font-size: 1.1rem; border-radius: 12px; border: 1px solid #dee2e6; }
        
        .adic-item-thumb { width: 44px; height: 44px; min-width: 44px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0; background: #fff; }
        .adic-item-fallback { width: 44px; height: 44px; min-width: 44px; border-radius: 10px; background: rgba(211, 16, 24, 0.08); display: flex; align-items: center; justify-content: center; color: var(--brand-red); }
        .molho-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <textarea id="json-adicionais" style="display:none;"><?= json_encode($adicionais, JSON_UNESCAPED_UNICODE) ?></textarea>

    <div id="view-home">

        <?php if (!$aberta): ?>
            <div class="alert alert-danger text-center mb-0 border-0 rounded-0 py-2 fw-bold animate__animated animate__fadeInDown small">
                <i class="fas fa-clock me-1"></i> ESTAMOS FECHADOS NO MOMENTO
            </div>
        <?php endif; ?>

        <!-- CABEÇALHO HERO COM LOGO DINÂMICA DA LOJA -->
        <header class="header-hero">
            <div class="header-overlay"></div>

            <div class="container d-flex justify-content-between align-items-center pt-3 position-relative" style="z-index: 5;">
                <a href="/" class="btn-top-nav" title="Voltar ao Início">
                    <i class="fas fa-arrow-left"></i>
                </a>

                <div class="d-flex gap-2">
                    <button type="button" class="btn-top-nav" onclick="focarBusca()" title="Buscar Lanches">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!$clienteLogado): ?>
                        <button type="button" class="btn-top-nav" onclick="abrirModal('modalLogin')" title="Entrar na Conta">
                            <i class="fas fa-user"></i>
                        </button>
                    <?php else: ?>
                        <a href="/<?= $slug ?>?acao_cliente=logout" class="btn-top-nav text-danger" title="Sair da Conta (<?= htmlspecialchars($clienteNome) ?>)">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="header-content">
                <!-- Logotipo da Loja com Fallback Seguro sem Loop -->
                <img src="<?= $logoImg ?>" class="header-logo-badge" alt="Logo" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/3075/3075977.png';">
                <div class="text-start">
                    <h1 class="header-brand-title"><?= htmlspecialchars($empresa->nome_empresa ?? 'HUGO LANCHES') ?></h1>
                    <p class="header-slogan"><?= htmlspecialchars($empresa->descricao_empresa ?: 'Sabor e qualidade em cada lanche!') ?></p>
                </div>
            </div>
        </header>

        <!-- CARD FLUTUANTE DE ENDEREÇO & WHATSAPP -->
        <div class="container" style="max-width: 600px;">
            <div class="contact-pill-card">
                <div class="d-flex align-items-center text-truncate">
                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                    <span class="text-truncate"><?= htmlspecialchars($enderecoFormatado) ?></span>
                </div>
                <div class="contact-divider"></div>
                <div class="d-flex align-items-center text-nowrap">
                    <i class="fab fa-whatsapp text-success me-2 fs-5"></i>
                    <span><?= htmlspecialchars($telefoneFormatado) ?></span>
                </div>
            </div>
        </div>

        <!-- BUSCA -->
        <div class="container mt-3" id="containerBusca" style="max-width: 700px;">
            <div class="position-relative">
                <input type="text" id="inputBusca" class="form-control rounded-pill px-4 py-2 border shadow-sm" placeholder="O que você deseja comer hoje?" onkeyup="filtrarCatalogo()">
            </div>
        </div>

        <div class="container mt-2" style="max-width: 700px;">

            <!-- CARROSSEL DE CATEGORIAS -->
            <div class="cat-scroll-wrapper" id="carousel-categorias">
                <?php 
                $primeiraCat = true;
                foreach ($categorias as $cat): 
                    $nomeCatLower = strtolower($cat->nome_cat);
                    $iconCat = 'fa-hamburger';

                    if (str_contains($nomeCatLower, 'frango')) {
                        $iconCat = 'fa-drumstick-bite';
                    } elseif (str_contains($nomeCatLower, 'churrasco') || str_contains($nomeCatLower, 'carne')) {
                        $iconCat = 'fa-bacon';
                    } elseif (str_contains($nomeCatLower, 'pernil') || str_contains($nomeCatLower, 'suino')) {
                        $iconCat = 'fa-piggy-bank';
                    } elseif (str_contains($nomeCatLower, 'bebida') || str_contains($nomeCatLower, 'refri')) {
                        $iconCat = 'fa-wine-bottle';
                    } elseif (str_contains($nomeCatLower, 'pizza')) {
                        $iconCat = 'fa-pizza-slice';
                    } elseif (str_contains($nomeCatLower, 'doce') || str_contains($nomeCatLower, 'sobremesa')) {
                        $iconCat = 'fa-ice-cream';
                    }
                ?>
                    <a href="javascript:void(0)" onclick="scrollToCategoria('sessao-<?= $cat->id ?>', this)" 
                       class="cat-pill <?= $primeiraCat ? 'active' : '' ?>" id="btn-cat-<?= $cat->id ?>">
                        <i class="fas <?= $iconCat ?> me-1"></i>
                        <span><?= htmlspecialchars($cat->nome_cat) ?></span>
                    </a>
                <?php 
                    $primeiraCat = false;
                endforeach; 
                ?>
            </div>

            <!-- PRODUTOS AGRUPADOS -->
            <main class="mt-3">
                <?php foreach ($categorias as $cat): ?>
                    <div id="sessao-<?= $cat->id ?>" class="categoria-bloco mb-4">
                        <div class="categoria-titulo-bloco">
                            <h2 class="categoria-nome"><?= htmlspecialchars($cat->nome_cat) ?></h2>
                            <a href="javascript:void(0)" onclick="focarBusca()" class="ver-mais-link">
                                Ver mais opções <i class="fas fa-chevron-right small"></i>
                            </a>
                        </div>

                        <div class="row g-2">
                            <?php 
                            foreach ($produtos as $p): 
                                if ($p->id_cat != $cat->id) continue;
                                $imgItem = (!empty($p->img_item) && $p->img_item !== 'false') 
                                    ? resolverUrlSegura($p->img_item, '') 
                                    : '';
                            ?>
                                <div class="col-12 card-produto-wrap" data-nome="<?= strtolower(htmlspecialchars($p->nome_item)) ?>">
                                    <div class="product-card-pro"
                                         data-id="<?= $p->id ?>"
                                         data-nome="<?= htmlspecialchars($p->nome_item) ?>"
                                         data-preco="<?= number_format($p->preco_item, 2, '.', '') ?>"
                                         data-img="<?= htmlspecialchars($imgItem) ?>"
                                         data-cat="<?= $p->id_cat ?>"
                                         onclick="abrirFluxoPedido(this)">
                                        
                                        <!-- Foto do Lanche -->
                                        <div class="prod-img-box">
                                            <?php if (!empty($imgItem)): ?>
                                                <img src="<?= $imgItem ?>" alt="<?= htmlspecialchars($p->nome_item) ?>" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'prod-img-fallback\'><i class=\'fas fa-hamburger\'></i></div>';">
                                            <?php else: ?>
                                                <div class="prod-img-fallback"><i class="fas fa-hamburger"></i></div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="prod-info">
                                            <div class="prod-title"><?= htmlspecialchars($p->nome_item) ?></div>
                                            <div class="prod-desc"><?= htmlspecialchars($p->descricao_item ?? '') ?></div>
                                            <div class="prod-price">R$ <?= number_format($p->preco_item, 2, ',', '.') ?></div>
                                        </div>

                                        <button type="button" class="btn-add-circle" title="Adicionar">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </main>

        </div>

        <!-- BARRA DO CARRINHO -->
        <div class="cart-bar-bottom animate__animated" id="barra-carrinho" onclick="abrirCheckout()" style="display:none;">
            <div class="d-flex align-items-center gap-3">
                <div class="cart-icon-wrap">
                    <i class="fas fa-shopping-cart text-danger fs-3"></i>
                    <span class="cart-badge-count" id="cart-qtd">0</span>
                </div>
                <div>
                    <b class="text-dark d-block" style="font-size: 1rem; line-height: 1.1;">Meu Carrinho</b>
                    <span class="fw-bold text-dark fs-6" id="cart-total-bar">R$ 0,00</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="btn btn-danger btn-sm rounded-pill fw-bold px-3">
                    Finalizar <i class="fas fa-arrow-right ms-1"></i>
                </span>
            </div>
        </div>

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
                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-3 fw-bold shadow" style="background: var(--brand-red);">ENTRAR NO CARDÁPIO</button>
                </form>
                <div class="mt-3 small">
                    Não tem conta? <a href="javascript:void(0)" onclick="abrirModal('modalCadastro')" class="text-danger fw-bold">Criar Cadastro</a>
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
                    <button type="submit" id="btnReg" class="btn btn-danger w-100 rounded-pill py-3 fw-bold shadow" style="background: var(--brand-red);" disabled>CADASTRAR E PEDIR</button>
                </form>
            </div>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- MODAL DO LANCHE: CARREGA A CAPA DA CONFIGURAÇÃO E FOTOS DOS EXTRAS  -->
    <!-- =================================================================== -->
    <div class="modal fade" id="modalFluxo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                    <h5 class="fw-bold mb-0 text-dark" id="m-nome">Detalhes do Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <div id="step-produto">
                        <!-- Imagem do Produto sem quebra ou loop -->
                        <img id="m-img" class="w-100 rounded-4 mb-3 shadow-sm" style="height: 190px; object-fit: cover;" alt="Foto">

                        <div class="bg-light p-3 rounded-4 mb-3 border d-flex justify-content-between align-items-center">
                            <div class="text-start">
                                <b class="text-dark d-block">Quantidade:</b>
                                <small class="text-muted">Quantos lanches deseja?</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn-qty" onclick="alterarQtdItem(-1)">-</button>
                                <input type="number" id="m-qtd-item" class="input-qty-main" value="1" min="1" max="999" maxlength="3" oninput="validarQtdDigitada(this)">
                                <button type="button" class="btn-qty text-danger fw-bold" onclick="alterarQtdItem(1)">+</button>
                            </div>
                        </div>

                        <!-- PERGUNTA SOBRE ADICIONAIS DO LANCHE -->
                        <div class="bg-warning bg-opacity-10 p-3 rounded-4 mb-3 border border-warning border-opacity-25" id="box-pergunta-adicionais">
                            <h6 class="fw-bold text-dark mb-1">Deseja adicionais para este lanche?</h6>
                            <small class="text-muted d-block mb-2">(Bacon, Queijo, Ovo, Cheddar, etc.)</small>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold btn-sm text-dark" onclick="irParaAdicionais()">SIM</button>
                                <button type="button" class="btn btn-light rounded-pill px-4 border btn-sm" onclick="finalizarSelecao()">NÃO</button>
                            </div>
                        </div>
                    </div>

                    <!-- LISTA DE ADICIONAIS COM FOTO DE CADA ITEM -->
                    <div id="step-adicionais" style="display:none;" class="text-start">
                        <h6 class="fw-bold text-dark mb-2">Selecione os adicionais do lanche:</h6>
                        <div id="lista-adicionais-dinamica" style="max-height: 240px; overflow-y: auto;"></div>
                        <button type="button" class="btn btn-light btn-sm w-100 mt-2 rounded-pill border fw-bold" onclick="voltarParaProduto()">Voltar ao Item</button>
                    </div>

                    <textarea id="m-obs" class="form-control mt-3 rounded-3 bg-light border-0" rows="2" placeholder="Observações do item (Ex: Sem cebola, bem passado...)"></textarea>
                </div>
                <div class="p-2 pt-0">
                    <button type="button" class="btn btn-danger w-100 rounded-pill fw-bold py-3 fs-6 shadow" style="background: var(--brand-red);" onclick="finalizarSelecao()">
                        CONFIRMAR - <span id="m-total-btn">R$ 0,00</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CHECKOUT FULLSCREEN -->
    <div id="view-checkout" class="checkout-layer p-4">
        <div class="container" style="max-width: 650px;">
            <div class="d-flex align-items-center mb-4">
                <button type="button" class="btn btn-light rounded-circle border me-3" onclick="fecharCheckout()"><i class="fas fa-arrow-left"></i></button>
                <h4 class="fw-bold mb-0">Finalizar Pedido</h4>
            </div>

            <div id="carrinho-itens-lista" class="mb-4"></div>

            <!-- ETAPA DE MOLHOS & COMPLEMENTOS DO PEDIDO COM FOTOS -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-light mb-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-danger p-2 rounded-circle me-2"><i class="fas fa-pepper-hot text-white"></i></span>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-dark mb-0">Molhos & Complementos do Pedido</h6>
                        <small class="text-muted">Selecione os sachês e molhos da sua preferência:</small>
                    </div>
                </div>

                <div class="alert alert-success border-0 rounded-3 py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="fas fa-gift me-1"></i> <b>Cortesia da Casa:</b> Até 2 molhos são grátis!
                    </div>
                    <span id="badge-cortesia-status" class="badge bg-success rounded-pill">2 cortesias restantes</span>
                </div>
                
                <div id="lista-molhos-checkout" class="row g-2"></div>
            </div>

            <!-- DADOS DE ENTREGA -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-light mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-map-marker-alt text-danger me-2"></i>Entrega</h6>
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
                <div class="d-flex justify-content-between mb-2 small text-secondary"><span>Subtotal Lanches:</span> <span id="f-subtotal">R$ 0,00</span></div>
                <div class="d-flex justify-content-between mb-2 small text-secondary"><span>Molhos Adicionais:</span> <span id="f-total-molhos">R$ 0,00</span></div>
                <div class="d-flex justify-content-between mb-2 small text-secondary"><span>Taxa de Entrega:</span> <span id="f-taxa">R$ 0,00</span></div>
                <div class="d-flex justify-content-between fw-bold fs-4 border-top border-secondary pt-2 mt-2">
                    <span>Total Geral:</span> <span class="text-danger" id="f-total" style="color: #ff4d4d !important;">R$ 0,00</span>
                </div>
            </div>

            <button type="button" id="btnConcluirPedido" class="btn btn-danger w-100 rounded-pill py-3 fw-bold fs-5 shadow-lg mb-5" style="background: var(--brand-red);" onclick="enviarPedidoFinal()">
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
        var molhosSelecionados = {};

        // Imagem oficial da capa da loja configurada no painel
        var headerImgLoja = '<?= $headerImg ?>';

        $(document).ready(function() {
            $('#login-tel, #reg-tel, #f-tel').mask('(00) 0 0000-0000');
            renderizarMolhosCheckout();

            <?php if ($showWelcome): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Bem-vindo, <?= addslashes($clienteNome) ?>!',
                    text: 'Que bom ter você de volta no cardápio.',
                    confirmButtonColor: '#d31018'
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

        function scrollToCategoria(id, elBtn) {
            if (elBtn) {
                document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('active'));
                elBtn.classList.add('active');
            }
            var el = document.getElementById(id);
            if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: "smooth" });
        }

        function focarBusca() {
            var input = document.getElementById('inputBusca');
            if (input) {
                input.focus();
                window.scrollTo({ top: input.offsetTop - 70, behavior: "smooth" });
            }
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

        // =========================================================
        // 1. ADICIONAIS DO LANCHE COM MINIATURA DAS FOTOS
        // =========================================================
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
            
            // Define a imagem do produto de forma segura sem loop
            var imgProdutoFinal = (d.img && d.img !== 'false' && d.img !== '') ? d.img : headerImgLoja;
            var mImg = document.getElementById('m-img');
            mImg.onerror = function() {
                this.onerror = null;
                this.src = headerImgLoja;
            };
            mImg.src = imgProdutoFinal;
            
            $("#m-qtd-item").val(1);
            recalcularTotalItemModal();

            $("#step-produto").show(); 
            $("#step-adicionais").hide(); 
            $("#m-obs").val("");

            // Filtra exclusivamente adicionais do lanche (exclui molhos gerais)
            var f = todosAdicionais.filter(a => {
                var cStr = String(a.categorias_adicional || '').trim().toLowerCase();
                if (cStr === 'molho') return false;
                var cats = cStr.split(',').map(s => s.trim());
                return cats.includes(String(d.cat)) || cats.includes('0') || cats.length === 0;
            });

            var h = "";
            f.forEach(a => {
                var adId = a.Id || a.id_adicionais;
                var adNome = a.nome_adicional;
                var adValor = parseFloat(a.valor_adicional || 0);
                var adFoto = (a.img_adicional && a.img_adicional !== '') ? ('/uploads/' + a.img_adicional) : '';

                h += `<div class="d-flex justify-content-between align-items-center mb-2 bg-light p-2 rounded-3 border">
                    <div class="d-flex align-items-center">
                        ${adFoto 
                            ? `<img src="${adFoto}" class="adic-item-thumb me-2" alt="${adNome}" onerror="this.onerror=null; this.outerHTML='<div class=\\'adic-item-fallback me-2\\'><i class=\\'fas fa-bacon\\'></i></div>';">`
                            : `<div class="adic-item-fallback me-2"><i class="fas fa-bacon"></i></div>`
                        }
                        <div>
                            <b class="text-dark small d-block">${adNome}</b>
                            <small class="text-danger fw-bold">${adValor > 0 ? '+ R$ ' + adValor.toLocaleString('pt-BR',{minimumFractionDigits:2}) : 'Grátis'}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-qty" onclick="qtdAd(${adId}, -1, ${adValor}, '${adNome}')">-</button>
                        <span id="q-val-${adId}" class="fw-bold text-dark fs-6" style="min-width: 18px; text-align: center;">0</span>
                        <button type="button" class="btn-qty text-danger" onclick="qtdAd(${adId}, 1, ${adValor}, '${adNome}')">+</button>
                    </div></div>`;
            });

            $("#lista-adicionais-dinamica").html(h || "<p class='small text-muted py-2'>Sem adicionais para este lanche.</p>");
            $("#box-pergunta-adicionais").toggle(f.length > 0);

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
            if (input.value.length > 3) input.value = input.value.slice(0, 3);
            var val = parseInt(input.value) || 1;
            if (val < 1) val = 1;
            if (val > 999) val = 999;
            itemTemp.quantidade = val;
            recalcularTotalItemModal();
        }

        function recalcularTotalItemModal() {
            var subUnitario = itemTemp.precoUnitario;
            itemTemp.adicionais.forEach(ad => { subUnitario += (ad.valor * ad.qtd); });
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
                $("#barra-carrinho").show().addClass("animate__fadeInUp");
                var totalGeral = carrinho.reduce((sum, i) => sum + i.totalItem, 0);
                var qtdTotalItens = carrinho.reduce((sum, i) => sum + i.quantidade, 0);
                $("#cart-qtd").text(qtdTotalItens);
                $("#cart-total-bar").text("R$ " + totalGeral.toLocaleString('pt-BR', {minimumFractionDigits:2}));
            }
        }

        // =========================================================
        // 2. MOLHOS NO CHECKOUT COM FOTOS DE SACHÊS E CORTESIA
        // =========================================================
        function renderizarMolhosCheckout() {
            var html = "";

            var molhosDisponiveis = todosAdicionais.filter(a => {
                var cStr = String(a.categorias_adicional || '').trim().toLowerCase();
                var nStr = String(a.nome_adicional || '').toLowerCase();
                return (cStr === 'molho' || nStr.includes('molho') || nStr.includes('ketchup') || nStr.includes('mostarda') || nStr.includes('maionese') || nStr.includes('sachê'));
            });

            molhosDisponiveis.forEach(a => {
                var adId = a.Id || a.id_adicionais;
                var adNome = a.nome_adicional;
                var adValor = parseFloat(a.valor_adicional || 0);
                var adImg = (a.img_adicional && a.img_adicional !== '') ? ('/uploads/' + a.img_adicional) : '';

                html += `
                <div class="col-12 col-md-6">
                    <div class="molho-card shadow-sm">
                        <!-- Foto do Sachê / Bisnaga -->
                        ${adImg 
                            ? `<img src="${adImg}" class="molho-img-thumb" alt="${adNome}" onerror="this.onerror=null; this.outerHTML='<div class=\\'molho-img-fallback\\'><i class=\\'fas fa-pepper-hot\\'></i></div>';">`
                            : `<div class="molho-img-fallback"><i class="fas fa-pepper-hot"></i></div>`
                        }

                        <!-- Informações do Molho -->
                        <div class="flex-grow-1" style="min-width: 0;">
                            <b class="text-dark small d-block text-truncate">${adNome}</b>
                            <span class="text-danger small fw-bold" id="rotulo-preco-molho-${adId}">
                                ${adValor > 0 ? '+ R$ ' + adValor.toLocaleString('pt-BR',{minimumFractionDigits:2}) : 'Cortesia da Casa'}
                            </span>
                        </div>

                        <!-- Contador de Quantidade -->
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn-qty" onclick="alterarQtdMolhoCheckout(${adId}, -1, ${adValor}, '${adNome}')">-</button>
                            <span id="q-molho-${adId}" class="fw-bold text-dark fs-6" style="min-width: 18px; text-align: center;">0</span>
                            <button type="button" class="btn-qty text-danger" onclick="alterarQtdMolhoCheckout(${adId}, 1, ${adValor}, '${adNome}')">+</button>
                        </div>
                    </div>
                </div>`;
            });

            $("#lista-molhos-checkout").html(html || "<p class='text-muted small mb-0'>Nenhum molho adicional cadastrado.</p>");
        }

        function alterarQtdMolhoCheckout(id, delta, valor, nome) {
            var el = $("#q-molho-" + id);
            var qtdAtual = parseInt(el.text()) || 0;
            qtdAtual += delta;
            if (qtdAtual < 0) qtdAtual = 0;
            if (qtdAtual > 99) qtdAtual = 99;
            el.text(qtdAtual);

            if (qtdAtual > 0) {
                molhosSelecionados[id] = { id: id, nome: nome, valor: valor, qtd: qtdAtual };
            } else {
                delete molhosSelecionados[id];
            }
            atualizarTotalFinal();
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
            var subtotalLanches = carrinho.reduce((sum, i) => sum + i.totalItem, 0);

            var totalQtdMolhos = 0;
            Object.values(molhosSelecionados).forEach(m => {
                totalQtdMolhos += m.qtd;
            });

            var cortesiasRestantes = Math.max(0, 2 - totalQtdMolhos);
            if (totalQtdMolhos <= 2) {
                $("#badge-cortesia-status").removeClass('bg-warning text-dark').addClass('bg-success text-white').text(cortesiasRestantes + " cortesia(s) restante(s)");
            } else {
                $("#badge-cortesia-status").removeClass('bg-success text-white').addClass('bg-warning text-dark').text("2 cortesias usadas | Extras cobrados");
            }

            var totalMolhosCobrados = 0;
            var unidadesGratisAplicadas = 0;

            Object.values(molhosSelecionados).forEach(m => {
                for (var u = 0; u < m.qtd; u++) {
                    if (unidadesGratisAplicadas < 2) {
                        unidadesGratisAplicadas++;
                    } else {
                        totalMolhosCobrados += m.valor;
                    }
                }
            });

            var taxa = parseFloat($("#f-bairro option:selected").data("taxa") || 0);
            var totalGeral = subtotalLanches + totalMolhosCobrados + taxa;

            $("#f-subtotal").text("R$ " + subtotalLanches.toLocaleString('pt-BR', {minimumFractionDigits:2}));
            
            if (totalMolhosCobrados > 0) {
                $("#f-total-molhos").html(`R$ ${totalMolhosCobrados.toLocaleString('pt-BR', {minimumFractionDigits:2})} <small class="text-success">(2 Grátis)</small>`);
            } else if (totalQtdMolhos > 0) {
                $("#f-total-molhos").html(`<span class="text-success fw-bold">Grátis (Cortesia)</span>`);
            } else {
                $("#f-total-molhos").text("R$ 0,00");
            }

            $("#f-taxa").text("R$ " + taxa.toLocaleString('pt-BR', {minimumFractionDigits:2}));
            $("#f-total").text("R$ " + totalGeral.toLocaleString('pt-BR', {minimumFractionDigits:2}));
        }

        function enviarPedidoFinal() {
            var btn = document.getElementById("btnConcluirPedido");
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
                    confirmButtonColor: '#d31018'
                });
                return;
            }

            if (carrinho.length === 0) {
                Swal.fire({ 
                    icon: 'info', 
                    title: 'Carrinho Vazio', 
                    text: 'Selecione ao menos um produto antes de concluir o pedido!',
                    confirmButtonColor: '#d31018'
                });
                return;
            }

            var subtotalLanches = carrinho.reduce((sum, i) => sum + i.totalItem, 0);
            var totalMolhosCobrados = 0;
            var unidadesGratis = 0;

            Object.values(molhosSelecionados).forEach(m => {
                for (var u = 0; u < m.qtd; u++) {
                    if (unidadesGratis < 2) unidadesGratis++;
                    else totalMolhosCobrados += m.valor;
                }
            });

            var totalGeral = subtotalLanches + totalMolhosCobrados + taxa;

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
                subtotal: (subtotalLanches + totalMolhosCobrados),
                total: totalGeral,
                itens: carrinho,
                molhos: Object.values(molhosSelecionados)
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
                    molhosSelecionados = {};
                    atualizarBarraCarrinho();
                    fecharCheckout();

                    Swal.fire({
                        icon: 'success',
                        title: 'Pedido Confirmado!',
                        html: `<div style="font-size: 1.15rem; line-height: 1.6;">
                                 Pedido número <b>#${res.codigo_pedido}</b> foi gerado com sucesso!<br>
                                 <span class="text-muted" style="font-size: 0.95rem;">Aguarde as atualizações no seu WhatsApp.</span>
                               </div>`,
                        confirmButtonColor: '#d31018',
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