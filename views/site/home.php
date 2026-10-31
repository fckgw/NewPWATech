<?php
/**
 * DIGITAL PWA - BDSoftech
 * Página de Montagem Dinâmica (PWA Cliente)
 * Desenvolvido por: BDSoftech
 */

// As variáveis $db (Conexão PDO) e $loja (Dados da Lanchonete) vêm do index.php
$tenant_id = $loja['id'];

try {
    // BUSCA OS INGREDIENTES VINCULADOS À BIBLIOTECA GLOBAL DE IMAGENS
    $sql = "SELECT 
                i.id, 
                i.nome, 
                i.preco, 
                i.categoria_ingrediente, 
                b.cam_imagem_grande, 
                b.cam_imagem_menor 
            FROM ingredientes i
            INNER JOIN biblioteca_imagens b ON i.biblioteca_id = b.id
            WHERE i.tenant_id = ? AND i.ativo = 1
            ORDER BY i.categoria_ingrediente, i.ordem ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$tenant_id]);
    $ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupa os ingredientes por categoria para o Menu
    $menu_agrupado = [];
    foreach ($ingredientes as $ing) {
        $menu_agrupado[$ing['categoria_ingrediente']][] = $ing;
    }
} catch (Exception $e) {
    die("Erro ao carregar cardápio: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Montar Pedido - <?= htmlspecialchars($loja['nome_fantasia']) ?></title>
    
    <!-- Bootstrap 5, Icons e Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bd-verde: #1A4D2E;
            --bd-amarelo: #F5C400;
            --bg-claro: #F8F9FA;
            --texto: #2D3436;
        }

        body { 
            background-color: var(--bg-claro); 
            color: var(--texto); 
            font-family: 'Poppins', sans-serif; 
            margin: 0; 
            overflow-x: hidden;
        }

        /* HEADER MOBILE */
        .header-loja {
            background: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        /* PALCO DE MONTAGEM (Onde o lanche aparece) */
        .stage-container {
            height: 380px;
            background: #fff;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image: radial-gradient(#f0f0f0 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* A TÁBUA DE MADEIRA */
        .wood-plate {
            position: absolute;
            bottom: 40px;
            width: 450px;
            max-width: 90%;
            z-index: 1;
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.15));
        }

        /* CAMADAS DO LANCHE (LAYERS) */
        .lanche-layer {
            position: absolute;
            width: 280px;
            max-width: 75%;
            z-index: 5;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            transform: translateY(-250px); /* Começa no alto para cair */
        }
        .lanche-layer.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* SELEÇÃO DE INGREDIENTES */
        .menu-scroll {
            padding: 20px;
            padding-bottom: 120px; /* Espaço para o rodapé */
            background: #fff;
            border-radius: 30px 30px 0 0;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.03);
            margin-top: -30px;
            position: relative;
            z-index: 1000;
        }

        .category-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #A0AEC0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 25px 0 15px 0;
        }

        /* CARDS DE INGREDIENTES (GRID) */
        .ing-card {
            background: #FDFDFD;
            border: 1px solid #F1F2F6;
            border-radius: 20px;
            padding: 12px;
            text-align: center;
            transition: 0.2s;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .ing-card:active { transform: scale(0.95); background: #fef9e7; }
        .ing-card img { width: 60px; height: 60px; object-fit: contain; margin-bottom: 10px; }
        .ing-card .name { font-size: 0.8rem; font-weight: 600; line-height: 1.2; margin-bottom: 5px; }
        .ing-card .price { font-size: 0.75rem; color: #27AE60; font-weight: 700; }

        /* RODAPÉ DE FINALIZAÇÃO */
        .footer-cart {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            padding: 15px 25px;
            border-top: 2px solid var(--bd-amarelo);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -5px 25px rgba(0,0,0,0.05);
            z-index: 1100;
        }

        .total-label { font-size: 0.7rem; color: #999; text-transform: uppercase; display: block; }
        .total-value { font-size: 1.5rem; font-weight: 700; color: var(--texto); }
        .btn-finish {
            background: var(--bd-verde);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-finish:hover { background: #000; color: var(--bd-amarelo); }

        /* AJUSTES PARA DESKTOP */
        @media (min-width: 992px) {
            .main-layout { display: flex; height: 100vh; }
            .stage-container { flex: 1; height: 100vh; }
            .menu-scroll { width: 450px; height: 100vh; overflow-y: auto; margin-top: 0; border-radius: 0; border-left: 1px solid #eee; }
            .footer-cart { width: 450px; right: 0; left: auto; }
        }
    </style>
</head>
<body>

<div class="main-layout">
    
    <!-- HEADER -->
    <header class="header-loja">
        <div class="d-flex align-items-center">
            <img src="/assets/img/logo_bdsoftech.png" height="35" class="me-2">
            <div>
                <span class="fw-bold d-block" style="font-size: 0.9rem; line-height: 1;"><?= htmlspecialchars($loja['nome_fantasia']) ?></span>
                <small class="text-success" style="font-size: 0.7rem;"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Aberto Agora</small>
            </div>
        </div>
        <a href="/<?= $loja['slug'] ?>/admin" class="btn btn-sm btn-light border rounded-pill fw-bold">ADMIN</a>
    </header>

    <!-- PALCO DE MONTAGEM (CANVAS CENTRAL) -->
    <section class="stage-container">
        <!-- Tábua/Chapa de Fundo (Imagem Estável) -->
        <img src="https://images.unsplash.com/photo-1580915411954-282cb1b0d780?q=80&w=600&auto=format&fit=crop" class="wood-plate">

        <!-- Container onde as camadas caem -->
        <div id="lanche-canvas">
            <!-- Camada base (Obrigatória no banco como Pão Base) -->
        </div>

        <div class="position-absolute bottom-0 mb-4 text-muted small opacity-50 d-md-none">
            Toque nos itens para montar seu lanche
        </div>
    </section>

    <!-- MENU DE SELEÇÃO -->
    <section class="menu-scroll">
        <h4 class="fw-bold mb-1">Monte seu Lanche</h4>
        <p class="text-muted small">Personalize cada detalhe do seu pedido.</p>

        <?php if (empty($menu_agrupado)): ?>
            <div class="alert alert-light border text-center mt-5">
                <i class="bi bi-info-circle d-block fs-2 mb-2"></i>
                Cardápio em manutenção.
            </div>
        <?php else: ?>
            <?php foreach ($menu_agrupado as $categoria => $itens): ?>
                <div class="category-title"><?= $categoria ?></div>
                <div class="row g-2">
                    <?php foreach ($itens as $i): ?>
                        <div class="col-4 col-sm-4 col-md-4 col-lg-6">
                            <div class="ing-card shadow-sm" 
                                 onclick="adicionarCamada('<?= $i['nome'] ?>', '<?= $i['preco'] ?>', '/assets/img/biblioteca/<?= $i['cam_imagem_grande'] ?>')">
                                
                                <!-- Imagem Menor (Ícone) da Biblioteca Global -->
                                <img src="/assets/img/biblioteca/<?= $i['cam_imagem_menor'] ?>" 
                                     onerror="this.src='https://cdn-icons-png.flaticon.com/512/3075/3075977.png'">
                                
                                <div class="name"><?= $i['nome'] ?></div>
                                <div class="price">+ R$ <?= number_format($i['preco'], 2, ',', '.') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- FOOTER (CHECKOUT) -->
    <footer class="footer-cart">
        <div>
            <span class="total-label">Total do Pedido</span>
            <div class="total-value" id="total-final">R$ 0,00</div>
        </div>
        <button class="btn-finish shadow" onclick="finalizar()">
            CONTINUAR <i class="bi bi-chevron-right ms-1"></i>
        </button>
    </footer>

</div>

<!-- Áudio de Feedback (Opcional) -->
<audio id="pop-sound" src="https://www.myinstants.com/media/sounds/pop-sound-effect_1.mp3"></audio>

<script>
    let precoTotal = 0;
    let contadorCamadas = 0;
    const som = document.getElementById('pop-sound');

    /**
     * Função que adiciona o item visualmente e no preço
     */
    function adicionarCamada(nome, preco, urlImagemGrande) {
        contadorCamadas++;
        
        // 1. Tocar som de feedback
        som.play();

        // 2. Adicionar a imagem no palco (Canvas)
        const canvas = document.getElementById('lanche-canvas');
        const img = document.createElement('img');
        img.src = urlImagemGrande;
        img.className = 'lanche-layer';
        
        // Lógica de Empilhamento (z-index e altura)
        // Cada item novo sobe 18px para criar o efeito 3D
        img.style.zIndex = 10 + contadorCamadas;
        img.style.bottom = (90 + (contadorCamadas * 18)) + 'px';
        
        canvas.appendChild(img);

        // Dispara a animação após um pequeno delay
        setTimeout(() => img.classList.add('active'), 50);

        // 3. Atualizar o Preço Total
        precoTotal += parseFloat(preco);
        document.getElementById('total-final').innerText = 'R$ ' + precoTotal.toFixed(2).replace('.', ',');

        // 4. Feedback tátil no celular (Vibração leve)
        if (navigator.vibrate) {
            navigator.vibrate(40);
        }
    }

    function finalizar() {
        if (precoTotal === 0) {
            alert("Selecione pelo menos um item para começar!");
            return;
        }
        alert("Pedido montado com sucesso! Próximo passo: Checkout.");
    }
</script>

</body>
</html>