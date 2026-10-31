<!-- Estrutura simplificada de CRUD usando Bootstrap 5 -->
<div class="card shadow-sm rounded-4 border-0 p-4">
    <div class="d-flex justify-content-between mb-4">
        <h4>Gerenciar Ingredientes</h4>
        <button class="btn btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#modalIngrediente">
            <i class="bi bi-plus"></i> Novo Ingrediente
        </button>
    </div>
    
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço Adicional</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop PHP buscando apenas os ingredientes do Hugo -->
            <?php foreach($meus_ingredientes as $ing): ?>
            <tr>
                <td><img src="/assets/img/layers/<?= $ing['imagem_montagem'] ?>" width="40"></td>
                <td><?= $ing['nome'] ?></td>
                <td><span class="badge bg-light text-dark"><?= $ing['categoria_ingrediente'] ?></span></td>
                <td>R$ <?= number_format($ing['preco'], 2, ',', '.') ?></td>
                <td><button class="btn btn-sm btn-outline-primary">Editar</button></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>