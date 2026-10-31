<div class="card p-4 rounded-4 border-0 shadow-sm">
    <h5 class="fw-bold mb-4">Adicionar ao meu Cardápio</h5>
    <form action="salvar_ingrediente.php" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="small fw-bold">Vincular à Biblioteca</label>
                <select name="biblioteca_id" class="form-select" required>
                    <option value="">Selecione um item visual...</option>
                    <?php
                    $itens = $db->query("SELECT * FROM biblioteca_imagens WHERE tab_status_id = 1")->fetchAll();
                    foreach ($itens as $it) {
                        echo "<option value='{$it['id']}'>{$it['nome_item']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="small fw-bold">Preço de Venda (Opcional)</label>
                <input type="number" step="0.01" name="preco" class="form-control" placeholder="0.00">
            </div>
            <div class="col-12 text-end">
                <button class="btn btn-primary rounded-pill px-4">ADICIONAR AO MEU MENU</button>
            </div>
        </div>
    </form>
</div>