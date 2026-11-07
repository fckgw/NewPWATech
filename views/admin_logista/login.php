<?php
$erro = $_GET['erro'] ?? ($_SESSION['login_erro'] ?? null);
unset($_SESSION['login_erro']);

$tenantSlug = $tenantSlug ?? ($_GET['tenant'] ?? '');
if (!empty($tenantSlug) && $tenantSlug !== 'admin-lojista') {
    $formAction = "/" . htmlspecialchars($tenantSlug) . "/admin";
} else {
    $formAction = "/admin-lojista";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Painel do Lojista | Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-lojista: #0e1726;
            --card-lojista: #1b2e4b;
            --accent-lojista: #34af23;
            --accent-hover: #2b931d;
        }

        body {
            background-color: var(--bg-lojista);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background-color: var(--card-lojista);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            width: 100%;
            max-width: 440px;
            padding: 40px;
        }

        .brand-badge {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-lojista), #10b981);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(52, 175, 35, 0.4);
        }

        .form-control {
            background-color: #0e1726;
            border: 1px solid #3b3f5c;
            color: #fff;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: #0e1726;
            border-color: var(--accent-lojista);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(52, 175, 35, 0.2);
        }

        .form-control::placeholder {
            color: #888ea8;
        }

        .btn-lojista {
            background: linear-gradient(135deg, var(--accent-lojista), var(--accent-hover));
            border: none;
            color: #fff;
            border-radius: 14px;
            padding: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(52, 175, 35, 0.3);
        }

        .btn-lojista:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(52, 175, 35, 0.45);
            background: linear-gradient(135deg, var(--accent-hover), #247c18);
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-badge">
                <i class="fas fa-store fa-2x text-white"></i>
            </div>
            <h4 class="fw-bold mb-1">Painel do Lojista</h4>
            <p class="text-secondary small">Gestão de Pedidos & Cardápio</p>
        </div>

        <?php if ($erro === 'credenciais_invalidas'): ?>
            <div class="alert alert-danger border-0 rounded-4 py-2 px-3 small text-center mb-4">
                <i class="fas fa-exclamation-triangle me-1"></i> E-mail ou senha incorretos!
            </div>
        <?php elseif ($erro === 'inativo'): ?>
            <div class="alert alert-warning border-0 rounded-4 py-2 px-3 small text-center mb-4">
                <i class="fas fa-ban me-1"></i> Usuário inativo ou assinatura pendente.
            </div>
        <?php elseif ($erro === 'campos_obrigatorios'): ?>
            <div class="alert alert-warning border-0 rounded-4 py-2 px-3 small text-center mb-4">
                <i class="fas fa-info-circle me-1"></i> Preencha todos os campos.
            </div>
        <?php endif; ?>

        <form action="<?= $formAction ?>" method="POST">
            <input type="hidden" name="acao" value="autenticar">

            <div class="mb-3">
                <label class="small fw-bold text-secondary mb-1 ms-1">E-MAIL DO LOJISTA</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 position-absolute" style="z-index: 10; left: 6px; top: 12px; color: #888ea8;">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="login_email" class="form-control ps-5" placeholder="seu-email@dominio.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                </div>
            </div>

            <div class="mb-2">
                <label class="small fw-bold text-secondary mb-1 ms-1">SENHA DE ACESSO</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 position-absolute" style="z-index: 10; left: 6px; top: 12px; color: #888ea8;">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="senha" id="passLojista" class="form-control ps-5" placeholder="••••••••••••" required />
                    <button type="button" class="btn position-absolute text-secondary" style="z-index: 10; right: 8px; top: 8px;" onclick="toggleLojistaPass()">
                        <i class="fas fa-eye" id="eyeLojista"></i>
                    </button>
                </div>
            </div>

            <div class="text-end mb-4">
                <a href="javascript:void(0)" onclick="abrirModalEsqueceuSenha()" class="text-success small fw-bold text-decoration-none">
                    Esqueceu a senha?
                </a>
            </div>

            <button type="submit" class="btn btn-lojista w-100 mb-3">
                ENTRAR NO PAINEL <i class="fas fa-arrow-right ms-2"></i>
            </button>

            <div class="text-center">
                <a href="/" class="text-secondary small text-decoration-none">
                    <i class="fas fa-chevron-left me-1"></i> Voltar para o Início
                </a>
            </div>
        </form>
    </div>

    <!-- MODAL ESQUECEU A SENHA -->
    <div class="modal fade" id="modalEsqueceuSenha" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-0 rounded-4 text-white p-4 shadow-lg">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-key text-success me-2"></i>Recuperar Acesso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <p class="text-secondary small mb-4">Informe o e-mail cadastrado na sua conta para gerarmos uma senha temporária imediata.</p>

                <form id="formRecuperarSenha" onsubmit="solicitarSenhaTemp(event)">
                    <div class="mb-4">
                        <label class="small fw-bold text-secondary mb-1">E-MAIL CADASTRADO</label>
                        <input type="email" id="recup_email" class="form-control" placeholder="seu-email@dominio.com" required>
                    </div>

                    <button type="submit" id="btnGerarTemp" class="btn btn-success w-100 rounded-pill fw-bold py-3 shadow">
                        <i class="fas fa-bolt me-1"></i> Gerar Senha Temporária
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let modalRecupInst = null;

        function toggleLojistaPass() {
            const input = document.getElementById('passLojista');
            const icon = document.getElementById('eyeLojista');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function abrirModalEsqueceuSenha() {
            document.getElementById('recup_email').value = document.getElementById('login_email').value || '';
            modalRecupInst = new bootstrap.Modal(document.getElementById('modalEsqueceuSenha'));
            modalRecupInst.show();
        }

        function solicitarSenhaTemp(e) {
            e.preventDefault();
            const btn = document.getElementById('btnGerarTemp');
            const email = document.getElementById('recup_email').value.trim();

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Gerando...';

            const formData = new FormData();
            formData.append('acao', 'recuperar_senha');
            formData.append('email', email);

            fetch('/admin-lojista', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Gerar Senha Temporária';

                if (res.sucesso) {
                    modalRecupInst.hide();

                    // Preenche imediatamente os campos e mostra a senha em texto claro com o olho aberto
                    document.getElementById('login_email').value = email;
                    document.getElementById('passLojista').value = res.senha_temp;
                    document.getElementById('passLojista').type = 'text';
                    document.getElementById('eyeLojista').classList.replace('fa-eye', 'fa-eye-slash');

                    Swal.fire({
                        icon: 'success',
                        title: 'Senha Temporária Gerada!',
                        html: `<p class="mb-2">Olá, <b>${res.nome}</b>. Sua nova senha de acesso provisória é:</p>
                               <div class="bg-dark p-3 rounded-3 mb-3 text-success fw-bold fs-3 border border-success border-opacity-25" id="boxSenhaTemp" style="letter-spacing: 3px;">
                                   ${res.senha_temp}
                               </div>
                               <small class="text-secondary">O formulário já foi preenchido. Basta clicar em <b>Entrar no Painel</b>.</small>`,
                        background: '#1b2e4b',
                        color: '#fff',
                        showCancelButton: true,
                        confirmButtonColor: '#34af23',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="fas fa-copy me-1"></i> Copiar Senha',
                        cancelButtonText: 'Fechar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            navigator.clipboard.writeText(res.senha_temp);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: res.mensagem || 'Falha ao recuperar senha.',
                        background: '#1b2e4b',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Gerar Senha Temporária';
                Swal.fire({
                    icon: 'error',
                    title: 'Falha de Conexão',
                    text: 'Não foi possível se comunicar com o servidor.',
                    background: '#1b2e4b',
                    color: '#fff',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    </script>
</body>
</html>