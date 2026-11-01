<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Ops! Ocorreu uma instabilidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #0e1726; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card-error { background: #1b2e4b; border-radius: 24px; padding: 40px; max-width: 600px; text-align: center; border: 1px solid rgba(255,255,255,0.08); }
    </style>
</head>
<body>
    <div class="card-error shadow-lg">
        <h2 class="fw-bold text-danger mb-3">⚠️ Detalhe da Exceção</h2>
        <p class="text-secondary mb-4"><?= htmlspecialchars($e->getMessage() ?? 'Erro desconhecido') ?></p>
        <div class="bg-black bg-opacity-40 p-3 rounded-3 text-start small text-muted mb-4" style="max-height: 200px; overflow-y: auto;">
            <b>Arquivo:</b> <?= htmlspecialchars($e->getFile()) ?> (linha <?= $e->getLine() ?>)<br>
            <b>Stack:</b><br><?= nl2br(htmlspecialchars($e->getTraceAsString())) ?>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-light rounded-pill px-4">Voltar</a>
    </div>
</body>
</html>