<?php
/**
 * PWA TECH - CONFIGURAÇÕES DA LOJA
 * Versão Completa: Identidade Visual, Regras de Negócio e Chave PIX
 */

$db = Conexao::getConexao();
$userIdLoja = (int)$loja['user_id'];

// BUSCA DADOS DA EMPRESA
$stmtEmp = $db->prepare("SELECT * FROM ws_empresa WHERE user_id = ? LIMIT 1");
$stmtEmp->execute([$userIdLoja]);
$empresa = $stmtEmp->fetch(PDO::FETCH_ASSOC);

// Lógica de Previsão de Imagem Segura
$caminhoUploads = '/uploads/';
$logoAtual = (!empty($empresa['img_logo']) && $empresa['img_logo'] !== 'false') ? $caminhoUploads . $empresa['img_logo'] : '/img/logo-default.png';
$capaAtual = (!empty($empresa['img_header']) && $empresa['img_header'] !== 'false') ? $caminhoUploads . $empresa['img_header'] : '/img/header-default.png';
?>

<div class="row g-4 animate__animated animate__fadeIn">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-white mb-0">Configurações do Estabelecimento</h3>
            <a href="/<?= $loja['user_virtual_env'] ?>" target="_blank" class="btn btn-outline-success rounded-pill fw-bold">
                <i class="fas fa-eye me-2"></i> Ver meu Cardápio Online
            </a>
        </div>
    </div>

    <form action="?secao=configuracoes&acao=salvar" method="POST" enctype="multipart/form-data" class="row g-4">
        
        <!-- COLUNA DA ESQUERDA: IMAGENS E REGRAS -->
        <div class="col-lg-4">
            <!-- CARD: LOGO -->
            <div class="card card-custom p-4 shadow mb-4">
                <h5 class="fw-bold text-white mb-4"><i class="fas fa-camera text-success me-2"></i> Logotipo</h5>
                <div class="text-center mb-3">
                    <div class="preview-logo-box mx-auto mb-3" style="width: 120px; height: 120px; border-radius: 25px; overflow: hidden; border: 3px solid rgba(255,255,255,0.1);">
                        <img src="<?= $logoAtual ?>" id="pre-logo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <input type="file" name="img_logo" class="form-control form-control-sm" accept="image/*" onchange="document.getElementById('pre-logo').src = window.URL.createObjectURL(this.files[0])">
                    <small class="text-secondary mt-2 d-block">Tamanho ideal: 500x500px (PNG)</small>
                </div>
            </div>

            <!-- CARD: CAPA -->
            <div class="card card-custom p-4 shadow mb-4">
                <h5 class="fw-bold text-white mb-4"><i class="fas fa-image text-success me-2"></i> Banner de Capa</h5>
                <div class="text-center mb-3">
                    <div class="preview-header-box mb-3" style="width: 100%; height: 120px; border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                        <img src="<?= $capaAtual ?>" id="pre-capa" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <input type="file" name="img_header" class="form-control form-control-sm" accept="image/*" onchange="document.getElementById('pre-capa').src = window.URL.createObjectURL(this.files[0])">
                    <small class="text-secondary mt-2 d-block">Tamanho ideal: 1200x450px</small>
                </div>
            </div>

            <!-- CARD: DELIVERY -->
            <div class="card card-custom p-4 shadow">
                <h5 class="fw-bold text-white mb-4"><i class="fas fa-motorcycle text-success me-2"></i> Regras de Entrega</h5>
                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">TEMPO ESTIMADO (MINUTOS)</label>
                    <input type="text" name="msg_tempo_delivery" class="form-control" value="<?= htmlspecialchars($empresa['msg_tempo_delivery']) ?>" placeholder="Ex: 40-60 min">
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-secondary mb-1">PEDIDO MÍNIMO (R$)</label>
                    <input type="text" name="minimo_delivery" class="form-control" value="<?= number_format($empresa['minimo_delivery'], 2, ',', '.') ?>">
                </div>
                <div class="mb-0">
                    <label class="small fw-bold text-secondary mb-1">TAXA DE ENTREGA PADRÃO (R$)</label>
                    <input type="text" name="config_delivery" class="form-control" value="<?= number_format($empresa['config_delivery'], 2, ',', '.') ?>">
                </div>
            </div>
        </div>

        <!-- COLUNA DA DIREITA: DADOS E PIX -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 shadow h-100">
                <h5 class="fw-bold text-white mb-4"><i class="fas fa-store-alt text-success me-2"></i> Informações da Loja</h5>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="small fw-bold text-secondary mb-1">NOME DA LANCHONETE *</label>
                        <input type="text" name="nome_empresa" class="form-control form-control-lg fw-bold" value="<?= htmlspecialchars($empresa['nome_empresa']) ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label class="small fw-bold text-secondary mb-1">DESCRIÇÃO / SLOGAN / SOBRE NÓS</label>
                        <textarea name="descricao_empresa" class="form-control" rows="3"><?= htmlspecialchars($empresa['descricao_empresa']) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold text-secondary mb-1">WHATSAPP DE PEDIDOS (COM DDD) *</label>
                        <input type="text" name="telefone_empresa" id="tel_mask" class="form-control" value="<?= htmlspecialchars($empresa['telefone_empresa']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold text-secondary mb-1">E-MAIL DE CONTATO</label>
                        <input type="email" name="email_empresa" class="form-control" value="<?= htmlspecialchars($empresa['email_empresa']) ?>">
                    </div>

                    <div class="col-md-12 mt-4 pt-4 border-top border-secondary">
                        <div class="bg-primary bg-opacity-10 p-4 rounded-4 border border-primary border-opacity-20">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-qrcode me-2"></i> Configuração de Recebimento via PIX</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="small fw-bold text-primary mb-1">SUA CHAVE PIX (CPF, Celular, E-mail ou Aleatória)</label>
                                    <input type="text" name="chave_pix" class="form-control bg-white text-dark fw-bold" value="<?= htmlspecialchars($empresa['chave_pix'] ?? '') ?>" placeholder="Digite sua chave para aparecer no checkout do cliente">
                                    <small class="text-secondary mt-1 d-block">Ao preencher, a opção de pagamento PIX será ativada automaticamente para seus clientes.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <h6 class="fw-bold text-white mb-3"><i class="fas fa-map-marker-alt text-success me-2"></i> Endereço para Retirada</h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="text" name="end_rua_n_empresa" class="form-control" placeholder="Rua e Número" value="<?= htmlspecialchars($empresa['end_rua_n_empresa']) ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="end_bairro_empresa" class="form-control" placeholder="Bairro" value="<?= htmlspecialchars($empresa['end_bairro_empresa']) ?>">
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="cidade_empresa" class="form-control" placeholder="Cidade" value="<?= htmlspecialchars($empresa['cidade_empresa']) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="end_uf_empresa" class="form-control text-center" maxlength="2" placeholder="UF" value="<?= htmlspecialchars($empresa['end_uf_empresa']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-3 fs-5 shadow-lg">
                        <i class="fas fa-save me-2"></i> SALVAR TODAS AS CONFIGURAÇÕES
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>