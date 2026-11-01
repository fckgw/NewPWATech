<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurações da Loja | <?= htmlspecialchars($loja['user_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0e1726; font-family: 'Plus Jakarta Sans', sans-serif; color: #fff; min-height: 100vh; }
        .sidebar { background: #060818; min-height: 100vh; padding: 25px 15px; }
        .nav-link { color: #888ea8; border-radius: 12px; margin-bottom: 6px; padding: 12px 18px; font-weight: 600; }
        .nav-link:hover, .nav-link.active { background: #34af23; color: #fff; }
        .card-custom { background: #1b2e4b; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .form-control, .form-select { background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 12px; padding: 12px 15px; }
        .form-control:focus { background-color: rgba(255,255,255,0.08); border-color: #34af23; color: #fff; box-shadow: none; }
        .preview-img { width: 90px; height: 90px; object-fit: cover; border-radius: 18px; border: 2px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR COM TODAS AS ABAS -->
        <aside class="col-md-2 sidebar d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-4 text-center"><i class="fas fa-store text-success me-2"></i><?= htmlspecialchars($loja['user_name']) ?></h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin"><i class="fas fa-columns me-2"></i> Pedidos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=produtos"><i class="fas fa-hamburger me-2"></i> Meus Produtos</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=categorias"><i class="fas fa-tags me-2"></i> Categorias</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=taxas"><i class="fas fa-motorcycle me-2"></i> Taxas / Bairros</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=horarios"><i class="fas fa-clock me-2"></i> Horários</a>
                    <a class="nav-link" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=clientes"><i class="fas fa-users me-2"></i> Meus Clientes (CRM)</a>
                    <a class="nav-link active" href="/<?= $loja['user_virtual_env'] ?>/admin?secao=configuracoes"><i class="fas fa-cog me-2"></i> Configurações</a>
                </nav>
            </div>
            <div>
                <a href="/<?= $loja['user_virtual_env'] ?>" target="_blank" class="btn btn-outline-success w-100 rounded-pill mb-2 fw-bold btn-sm">Ver Cardápio</a>
                <a href="/<?= $loja['user_virtual_env'] ?>/admin?acao=logout" class="btn btn-outline-danger w-100 rounded-pill fw-bold btn-sm">Sair</a>
            </div>
        </aside>

        <main class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Identidade Visual & Configurações da Loja</h3>
                    <p class="text-secondary small">Altere nome, capa, logo, telefone do WhatsApp e regras de atendimento.</p>
                </div>
            </div>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success border-0 rounded-4 py-2 px-3 small mb-4">
                    <i class="fas fa-check-circle me-1"></i> Configurações da loja salvas com sucesso!
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row g-4">

                    <!-- IDENTIDADE & IMAGENS -->
                    <div class="col-md-4">
                        <div class="card card-custom p-4 mb-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-image text-success me-2"></i>Logo & Capa</h5>

                            <div class="mb-3 text-center">
                                <label class="small fw-bold d-block text-secondary mb-2">LOGO ATUAL</label>
                                <img src="/uploads/<?= htmlspecialchars($empresa['img_logo'] ?? 'logo-default.png') ?>" class="preview-img mb-2" onerror="this.src='/img/burger.png'">
                                <input type="file" name="img_logo" class="form-control form-control-sm">
                            </div>

                            <div class="mb-3 text-center">
                                <label class="small fw-bold d-block text-secondary mb-2">BANNER / CAPA DO CARDÁPIO</label>
                                <img src="/uploads/<?= htmlspecialchars($empresa['img_header'] ?? 'header-default.png') ?>" class="w-100 rounded-3 mb-2" style="height: 90px; object-fit: cover;" onerror="this.src='/img/burger.png'">
                                <input type="file" name="img_header" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="card card-custom p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-motorcycle text-success me-2"></i>Regras de Pedido</h5>
                            <div class="mb-3">
                                <label class="small fw-bold text-secondary">TEMPO ESTIMADO DE ENTREGA</label>
                                <input type="text" name="msg_tempo_delivery" class="form-control" value="<?= htmlspecialchars($empresa['msg_tempo_delivery'] ?? '30-60 min') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-secondary">VALOR MÍNIMO DO PEDIDO (R$)</label>
                                <input type="text" name="minimo_delivery" class="form-control" value="<?= number_format((float)($empresa['minimo_delivery'] ?? 0), 2, ',', '.') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-secondary">TAXA FIXA PADRÃO (R$)</label>
                                <input type="text" name="config_delivery" class="form-control" value="<?= number_format((float)($empresa['config_delivery'] ?? 0), 2, ',', '.') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- DADOS CADASTRAIS & CONTATO -->
                    <div class="col-md-8">
                        <div class="card card-custom p-4">
                            <h5 class="fw-bold mb-3"><i class="fas fa-store text-success me-2"></i>Informações Principais</h5>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary">NOME FANTASIA DA LANCHONETE</label>
                                <input type="text" name="nome_empresa" class="form-control" value="<?= htmlspecialchars($empresa['nome_empresa'] ?? $loja['user_name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary">SLOGAN / DESCRIÇÃO CURTA</label>
                                <textarea name="descricao_empresa" class="form-control" rows="2"><?= htmlspecialchars($empresa['descricao_empresa'] ?? '') ?></textarea>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-secondary">WHATSAPP DA LOJA (RECEBE OS PEDIDOS)</label>
                                    <input type="tel" name="telefone_empresa" class="form-control" value="<?= htmlspecialchars($empresa['telefone_empresa'] ?? $loja['user_telefone']) ?>" placeholder="(11) 99999-9999">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-secondary">E-MAIL DE CONTATO</label>
                                    <input type="email" name="email_empresa" class="form-control" value="<?= htmlspecialchars($empresa['email_empresa'] ?? $loja['user_email']) ?>">
                                </div>
                            </div>

                            <h5 class="fw-bold mt-4 mb-3"><i class="fas fa-map-marker-alt text-success me-2"></i>Endereço Físico</h5>

                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="small fw-bold text-secondary">RUA E NÚMERO</label>
                                    <input type="text" name="end_rua_n_empresa" class="form-control" value="<?= htmlspecialchars($empresa['end_rua_n_empresa'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-secondary">BAIRRO</label>
                                    <input type="text" name="end_bairro_empresa" class="form-control" value="<?= htmlspecialchars($empresa['end_bairro_empresa'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="small fw-bold text-secondary">CIDADE</label>
                                    <input type="text" name="cidade_empresa" class="form-control" value="<?= htmlspecialchars($empresa['cidade_empresa'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-secondary">ESTADO (UF)</label>
                                    <input type="text" name="end_uf_empresa" class="form-control" value="<?= htmlspecialchars($empresa['end_uf_empresa'] ?? 'SP') ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success rounded-pill fw-bold px-5 py-3 shadow">
                                <i class="fas fa-save me-2"></i> Salvar Todas as Configurações
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>