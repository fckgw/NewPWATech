<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Digital PWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: 'Poppins', sans-serif; display: flex; align-items: center; min-height: 100vh; }
        .login-card { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); overflow: hidden; }
        .login-header { background: #1A4D2E; color: white; padding: 40px; text-align: center; }
        .btn-bd { background: #1A4D2E; color: white; border-radius: 12px; padding: 12px; border: none; width: 100%; font-weight: 600; }
        .btn-bd:hover { background: #F5C400; color: #000; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card">
                    <div class="login-header">
                        <img src="/assets/img/logo_bdsoftech.png" width="100" class="bg-white p-2 rounded-3 mb-3">
                        <h4 class="mb-0 fw-bold">Acesso ao Sistema</h4>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <form action="/app/Controllers/LoginController.php" method="POST">
                            <div class="mb-3">
                                <label class="small fw-bold">Seu E-mail</label>
                                <input type="email" name="email" class="form-control form-control-lg rounded-3" placeholder="email@exemplo.com" required>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold">Sua Senha</label>
                                <input type="password" name="senha" class="form-control form-control-lg rounded-3" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-bd">ENTRAR</button>
                        </form>
                        <div class="text-center mt-4">
                            <a href="/" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Voltar ao site</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>