<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PWA Food | Plataforma de Cardápio Digital Multi-Lojas</title>

    <!-- Bootstrap 5, FontAwesome e Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0b1016;
            --pwa-green: #34af23;
            --pwa-green-hover: #2b931d;
            --master-purple: #6366f1;
            --master-purple-hover: #4f46e5;
        }

        body {
            background-color: var(--bg-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(11, 16, 22, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 0;
        }

        /* Botão Login Lojista (Verde) */
        .btn-lojista-nav {
            background: linear-gradient(135deg, var(--pwa-green), #10b981);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 175, 35, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-lojista-nav:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 175, 35, 0.5);
        }

        /* Botão Login Master (Roxo / Discreto) */
        .btn-master-nav {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 10px 22px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-master-nav:hover {
            background: var(--master-purple);
            border-color: var(--master-purple);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        /* Hero */
        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 20px;
            background: radial-gradient(circle at 50% 20%, rgba(52, 175, 35, 0.12), transparent 70%);
        }

        .hero-badge {
            display: inline-block;
            background: rgba(52, 175, 35, 0.15);
            border: 1px solid rgba(52, 175, 35, 0.3);
            color: #4ade80;
            border-radius: 50px;
            padding: 6px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .hero-title span {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            color: #94a3b8;
            font-size: 1.15rem;
            max-width: 650px;
            margin: 0 auto 40px;
        }

        /* Cards Rápidos de Login no Centro */
        .card-acesso {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 30px 25px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #fff;
            display: block;
            height: 100%;
        }
        .card-acesso:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }
        .card-acesso.lojista:hover {
            border-color: rgba(52, 175, 35, 0.4);
            box-shadow: 0 15px 35px rgba(52, 175, 35, 0.15);
        }
        .card-acesso.master:hover {
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.15);
        }

        .icon-circle {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            font-size: 1.3rem;
        }
        .icon-lojista {
            background: rgba(52, 175, 35, 0.2);
            color: #4ade80;
        }
        .icon-master {
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
        }
    </style>
</head>
<body>

    <!-- NAVBAR COM OS DOIS BOTÕES DE ACESSO -->
    <nav class="navbar navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-white fw-bold" href="/">
                <i class="fas fa-qrcode text-success fa-lg me-2"></i>
                <span>PWA<span class="text-success">Food</span></span>
            </a>

            <!-- Ações Rápidas no Topo -->
            <div class="d-flex align-items-center gap-2">
                <!-- BOTÃO 1: LOGIN MASTER -->
                <a href="/master-admin" class="btn-master-nav">
                    <i class="fas fa-shield-halved me-2"></i> Login Master
                </a>

                <!-- BOTÃO 2: LOGIN LOJISTA -->
                <a href="/admin-lojista" class="btn-lojista-nav">
                    <i class="fas fa-store me-2"></i> Login Lojista
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section text-center">
        <div class="container">
            <span class="hero-badge">
                <i class="fas fa-bolt me-1"></i> Multi-Tenant Delivery PWA
            </span>

            <h1 class="hero-title">
                Seu Cardápio Digital Inteligente<br>
                <span>Sem Taxas Abusivas</span>
            </h1>

            <p class="hero-desc">
                Solução completa com tecnologia PWA para lanchonetes, restaurantes e pizzarias receberem pedidos diretamente no WhatsApp e no painel de controle.
            </p>

            <!-- CARDS DE ACESSO RÁPIDO NO CENTRO -->
            <div class="row justify-content-center g-4 mt-2" style="max-width: 800px; margin: 0 auto;">
                
                <!-- CARD DO LOJISTA -->
                <div class="col-md-6 text-start">
                    <a href="/admin-lojista" class="card-acesso lojista">
                        <div class="icon-circle icon-lojista">
                            <i class="fas fa-store"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Área do Lojista</h5>
                        <p class="text-secondary small mb-3">
                            Acesse seu painel administrativo para gerenciar cardápio, produtos, pedidos recebidos e formas de entrega.
                        </p>
                        <span class="text-success fw-bold small">
                            Acessar Painel da Loja <i class="fas fa-arrow-right ms-1"></i>
                        </span>
                    </a>
                </div>

                <!-- CARD DO MASTER ADMIN -->
                <div class="col-md-6 text-start">
                    <a href="/master-admin" class="card-acesso master">
                        <div class="icon-circle icon-master">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Painel Master</h5>
                        <p class="text-secondary small mb-3">
                            Área exclusiva do proprietário da plataforma para gerenciar assinaturas, planos e liberar novos lojistas.
                        </p>
                        <span class="text-primary fw-bold small" style="color: #818cf8 !important;">
                            Acessar Painel Central <i class="fas fa-arrow-right ms-1"></i>
                        </span>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER SIMPLES -->
    <footer class="py-4 text-center text-secondary small border-top border-secondary border-opacity-10">
        <div class="container">
            © <?= date('Y') ?> PWA Food Platform. Todos os direitos reservados.
        </div>
    </footer>

</body>
</html>