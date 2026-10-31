<?php
// Lógica para salvar os arquivos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_item'];
    $dir = "../../assets/img/biblioteca/";
    
    // Upload da Grande (Layer)
    $ext_g = pathinfo($_FILES['img_grande']['name'], PATHINFO_EXTENSION);
    $nome_g = time() . "_grande." . $ext_g;
    move_uploaded_file($_FILES['img_grande']['tmp_name'], $dir . $nome_g);

    // Upload da Menor (Ícone)
    $ext_m = pathinfo($_FILES['img_menor']['name'], PATHINFO_EXTENSION);
    $nome_m = time() . "_menor." . $ext_m;
    move_uploaded_file($_FILES['img_menor']['tmp_name'], $dir . $nome_m);

    // Salva no Banco
    $stmt = $db->prepare("INSERT INTO biblioteca_imagens (nome_item, cam_imagem_grande, cam_imagem_menor) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $nome_g, $nome_m]);
}
?>

<div class="card p-4 rounded-4 border-0 shadow-sm">
    <h4 class="fw-bold mb-4">Gerenciar Biblioteca Global</h4>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
            <label class="small fw-bold">Nome do Item (Pão, Carne, etc)</label>
            <input type="text" name="nome_item" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="small fw-bold">Imagem Grande (Layer PNG)</label>
            <input type="file" name="img_grande" class="form-control" accept="image/png" required>
        </div>
        <div class="col-md-4">
            <label class="small fw-bold">Imagem Menor (Ícone Cardápio)</label>
            <input type="file" name="img_menor" class="form-control" accept="image/png" required>
        </div>
        <div class="col-12 mt-3">
            <button class="btn btn-success px-5 rounded-pill fw-bold">SUBIR PARA BIBLIOTECA</button>
        </div>
    </form>
</div>